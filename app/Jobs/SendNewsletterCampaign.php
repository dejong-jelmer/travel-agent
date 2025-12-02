<?php

namespace App\Jobs;

use App\Models\NewsletterCampaign;
use App\Services\NewsletterCampaignService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendNewsletterCampaign implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public NewsletterCampaign $campaign)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(NewsletterCampaignService $service): void
    {
        $service->sendCampaign($this->campaign);
    }

    public function failed(\Throwable $exception): void
      {
          Log::error('Newsletter campaign send job failed', [
              'campaign_id' => $this->campaign->id,
              'error' => $exception->getMessage(),
          ]);
      }
}
