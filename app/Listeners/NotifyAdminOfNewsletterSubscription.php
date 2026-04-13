<?php

namespace App\Listeners;

use App\Events\NewsletterSubscriptionRequested;
use App\Mail\AdminNewsletterSubscriptionMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotifyAdminOfNewsletterSubscription implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(NewsletterSubscriptionRequested $event): void
    {
        $address = config('contact.mail');

        try {
            Mail::to($address)->send(
                new AdminNewsletterSubscriptionMail($event->subscriber)
            );
        } catch (\Throwable $e) {
            Log::error('Admin newsletter subscription notification mail failed', [
                'subscriber_id' => $event->subscriber->id,
                'subscriber_email' => $event->subscriber->email,
                'admin_email' => $address,
                'exception' => $e,
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(NewsletterSubscriptionRequested $event, \Throwable $exception): void
    {
        Log::critical('Admin newsletter subscription notification email permanently failed after retries', [
            'subscriber_id' => $event->subscriber->id,
            'subscriber_email' => $event->subscriber->email,
            'admin_email' => config('contact.mail'),
            'error' => $exception->getMessage(),
        ]);
    }
}
