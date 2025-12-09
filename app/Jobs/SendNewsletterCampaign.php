<?php

namespace App\Jobs;

use App\Enums\Newsletter\CampaignStatus;
use App\Enums\Newsletter\CampaignSubscriberStatus;
use App\Mail\NewsletterCampaignMail;
use App\Models\NewsletterCampaign;
use App\Models\NewsletterCampaignSubscriber;
use App\Models\NewsletterSubscriber;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendNewsletterCampaign implements ShouldQueue
{
    use Queueable;

    /**
     * Determine number of times the job may be attempted.
     */
    public int $tries;

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout;

    /**
     * Calculate the number of seconds to wait before retrying the job.
     */
    public array $backoff;

    /**
     * Create a new job instance.
     */
    private int $sentCount = 0;

    private int $failedCount = 0;

    public function __construct(
        public NewsletterCampaign $campaign,
        public array $subscriberIds,
    ) {
        $this->tries = config('newsletter.campaign.tries', 3);
        $this->timeout = config('newsletter.campaign.timeout', 300);
        $this->backoff = config('newsletter.campaign.backoff', [60, 300, 900]);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $subscribers = NewsletterSubscriber::whereIn('id', $this->subscriberIds)->get();

        foreach ($subscribers as $subscriber) {
            $pivot = NewsletterCampaignSubscriber::firstOrNew([
                'campaign_id' => $this->campaign->id,
                'subscriber_id' => $subscriber->id,
            ], [
                'status' => CampaignSubscriberStatus::Queued,
            ]);

            // Skip if already processed
            if (
                $pivot->exists
                && in_array($pivot->status, [
                    CampaignSubscriberStatus::Sent,
                    CampaignSubscriberStatus::Failed,
                ], strict: true)
            ) {
                continue;
            }

            try {
                Mail::to($subscriber)->send(new NewsletterCampaignMail($this->campaign, $subscriber));
                DB::transaction(function () use ($pivot) {
                    $pivot->status = CampaignSubscriberStatus::Sent;
                    $pivot->sent_at = now();
                    $pivot->save();
                });
                $this->sentCount++;
            } catch (Throwable $e) {
                $this->failedCount++;

                DB::transaction(function () use ($pivot, $e) {
                    $pivot->status = CampaignSubscriberStatus::Failed;
                    $pivot->error_message = $e->getMessage();
                    $pivot->failed_at = now();
                    $pivot->save();
                });

                Log::error('Failed to send newsletter', [
                    'campaign_id' => $this->campaign->id,
                    'subscriber_id' => $subscriber->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
        $this->updateCounters($this->campaign, $this->sentCount, $this->failedCount);

        $this->campaign = $this->campaign->fresh();
        if ($this->campaign->sent_count >= $this->campaign->total_recipients) {
            DB::transaction(function () {
                $campaign = NewsletterCampaign::lockForUpdate()->find($this->campaign->id);
                $campaign->update([
                    'status' => CampaignStatus::Sent,
                    'sent_at' => now(),
                ]);
            });
        }
    }

    public function failed(Throwable $exception): void
    {
        $newFailedCount = count($this->subscriberIds) - $this->sentCount - $this->failedCount;

        if ($newFailedCount > 0) {
            $this->campaign->increment('failed_count', $newFailedCount);
        }

        $this->campaign = $this->campaign->fresh();

        DB::transaction(function () {
            $campaign = NewsletterCampaign::lockForUpdate()->find($this->campaign->id);
            $campaign->update([
                'status' => CampaignStatus::Failed,
                'failed_at' => now(),
            ]);
        });

        foreach ($this->subscriberIds as $subscriberId) {
            NewsletterCampaignSubscriber::firstOrCreate([
                'campaign_id' => $this->campaign->id,
                'subscriber_id' => $subscriberId,
            ], [
                'status' => CampaignSubscriberStatus::Failed,
                'error_message' => 'Job failed: '.$exception->getMessage(),
                'failed_at' => now(),
            ]);
        }

        // @todo notify admin of failed job
        Log::critical('Newsletter campaign job failed completely', [
            'campaign_id' => $this->campaign->id,
            'subscriber_ids' => $this->subscriberIds,
            'error' => $exception->getMessage(),
        ]);
    }

    /**
     * Update campaign counters
     *
     * @return NewsletterCampaign $campaign
     */
    private function updateCounters(NewsletterCampaign $campaign, int $sentCount, int $failedCount): void
    {
        if ($sentCount > 0) {
            $campaign->increment('sent_count', $sentCount);
        }

        if ($failedCount > 0) {
            $campaign->increment('failed_count', $failedCount);
        }
    }
}
