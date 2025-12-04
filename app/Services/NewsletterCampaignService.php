<?php

namespace App\Services;

use App\Enums\Newsletter\CampaignStatus;
use App\Exceptions\CampaignAlreadySentException;
use App\Jobs\SendNewsletterCampaign;
use App\Models\NewsletterCampaign;
use App\Models\NewsletterSubscriber;
use Illuminate\Support\Facades\DB;

class NewsletterCampaignService
{
    /**
     * Update the $campaign status to Queued with a timestamp &
     * Send all active newsletter subscribers the newsletter campaign via
     * the App\Jobs\SendNewsletterCampaign job by chunks
     *
     * @throws App\Exceptions\CampaignAlreadySentException
     */
    public function sendCampaign(NewsletterCampaign $campaign): void
    {
        $totalRecipients = 0;
        $chunkSize = config('newsletter.campaign.chunk_size');

        // Atomic check and update within transaction to prevent race conditions
        DB::transaction(function () use ($campaign, &$totalRecipients) {
            $campaign = NewsletterCampaign::lockForUpdate()->findOrFail($campaign->id);
            // Check if campaign has not been sent or queued
            if ($this->checkIfAlreadySent($campaign->status)) {
                throw new CampaignAlreadySentException(
                    __('newsletter.campaign.campaign_already_sent', ['campaign_id' => $campaign->id])
                );
            }
            $totalRecipients = NewsletterSubscriber::active()->count();

            //  If no recipients, mark as sent immediately and return
            if ($totalRecipients === 0) {
                $campaign->update([
                    'status' => CampaignStatus::Sent,
                    'sent_at' => now(),
                    'queued_at' => now(),
                    'total_recipients' => 0,
                ]);

                return;
            }

            // Update campaign to Queued status
            $campaign->update([
                'status' => CampaignStatus::Queued,
                'queued_at' => now(),
                'total_recipients' => $totalRecipients,
            ]);
        });
        // No need to proceed
        if ($totalRecipients === 0) {
            return;
        }

        // Eager load relations used in mail (outside transaction for performance)
        $campaign->load(['heroImage', 'trips.heroImage']);

        // Dispatch SendNewsletterCampaign job in chunks
        NewsletterSubscriber::active()->chunkById($chunkSize, function ($subscribers) use ($campaign) {
            SendNewsletterCampaign::dispatch($campaign, $subscribers->modelKeys());
        });
    }

    /**
     * Check if campaign status is sent or queued
     */
    private function checkIfAlreadySent(CampaignStatus $status): bool
    {
        return in_array($status, [
            CampaignStatus::Queued,
            CampaignStatus::Sent,
        ], strict: true);
    }
}
