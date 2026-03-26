<?php

namespace App\Console\Commands\Newsletter;

use App\Models\NewsletterSubscriber;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PurgeUnsubscribedSubscribers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'newsletter:purge-unsubscribed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete newsletter subscribers who unsubscribed more than 3 months ago';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $retentionMonths = (int) config('newsletter.subscription.retention_months', 3);

        $count = NewsletterSubscriber::unsubscribed()
            ->where('unsubscribed_at', '<=', now()->subMonths($retentionMonths))
            ->delete();

        if ($count === 0) {
            $this->info('No subscribers to purge.');

            return self::SUCCESS;
        }

        $this->info("Purged {$count} unsubscribed subscriber(s).");
        Log::info("Purged {$count} unsubscribed newsletter subscriber(s) older than {$retentionMonths} months.");

        return self::SUCCESS;
    }
}
