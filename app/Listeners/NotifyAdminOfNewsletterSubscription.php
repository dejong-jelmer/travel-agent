<?php

namespace App\Listeners;

use App\Events\NewsletterSubscriptionRequested;
use App\Mail\AdminNewsletterSubscriptionMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotifyAdminOfNewsletterSubscription
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(NewsletterSubscriptionRequested $event): void
    {
        $address = config('booking.mail');

        try {
            Mail::to($address)->queue(
                new AdminNewsletterSubscriptionMail($event->subscriber)
            );
        } catch (\Throwable $e) {
            Log::error('Admin newsletter subscription notification mail failed: '.$e->getMessage(), [
                'subscriber_id' => $event->subscriber->id,
                'subscriber_email' => $event->subscriber->email,
                'admin_email' => $address,
            ]);
            Log::error('Stack trace: '.$e->getTraceAsString());
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
            'admin_email' => config('booking.mail'),
            'error' => $exception->getMessage(),
        ]);
    }
}
