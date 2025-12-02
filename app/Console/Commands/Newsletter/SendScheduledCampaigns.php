<?php

namespace App\Console\Commands\Newsletter;

use App\Enums\Newsletter\CampaignStatus;
use App\Jobs\SendNewsletterCampaign;
use App\Models\NewsletterCampaign;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendScheduledCampaigns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'newsletter:send-scheduled-campaigns';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a scheduled newsletter campaign to all subscribers';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $campaigns = NewsletterCampaign::where('status', CampaignStatus::Scheduled)
            ->where('scheduled_at', '<=', now())
            ->get();

        foreach ($campaigns as $campaign) {
            try {
                SendNewsletterCampaign::dispatch($campaign);
                $this->info("Dispatched campaign: {$campaign->subject}");
            } catch (\Exception $e) {
                // Silent fail - log en ga door
                Log::warning('Failed to dispatch scheduled campaign', [
                    'campaign_id' => $campaign->id,
                    'subject' => $campaign->subject,
                    'error' => $e->getMessage(),
                ]);
                $this->warn("Skipped campaign {$campaign->id}: {$e->getMessage()}");
            }
        }
    }
}
