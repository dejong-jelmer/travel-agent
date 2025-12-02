<?php

namespace App\Services;

use App\Enums\Newsletter\CampaignStatus;
use App\Exceptions\CampaignAlreadySentException;
use App\Mail\NewsletterCampaignMail;
use App\Models\NewsletterCampaign;
use App\Models\NewsletterSubscriber;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NewsletterCampaignService
{
    public function sendCampaign(NewsletterCampaign $campaign): void
    {
        $sentCount = 0;
        $chunkSize = config('newsletter.campaign.chunk_size');

        if (in_array($campaign->status, [
            CampaignStatus::Sending,
            CampaignStatus::Sent
        ], strict: true)) {
            throw new CampaignAlreadySentException("Campaign: '{$campaign->subject}', was already sent");
        }

        DB::transaction(function () use ($campaign, &$sentCount, $chunkSize) {

            $campaign->update(['status' => CampaignStatus::Sending]);

            NewsletterSubscriber::active()->chunk($chunkSize, function ($subscribers) use ($campaign, &$sentCount) {
                foreach ($subscribers as $subscriber) {
                    try {
                        Mail::to($subscriber)->queue(new NewsletterCampaignMail($campaign, $subscriber));
                        $sentCount++;
                    } catch (Exception $e) {
                        Log::error('Failed to queue newsletter', [
                            'campaign_id' => $campaign->id,
                            'subscriber_id' => $subscriber->id,
                        ]);
                    }
                }
            });

            $campaign->update([
                'status' => CampaignStatus::Sent,
                'sent_at' => now(),
                'sent_count' => $sentCount,
            ]);
        });
    }
}
