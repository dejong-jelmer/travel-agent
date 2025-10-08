<?php

namespace App\Listeners;

use App\Events\NewsletterSubscriptionRequested;
use App\Mail\Newsletter\Confirmation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendNewsletterConfirmationEmail
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
        $subscriber = $event->subscriber;
        $address = $subscriber->email;

        try {
            Mail::to($address)->send(
                new Confirmation($subscriber)
            );
        } catch (\Throwable $e) {
            Log::error('Mail sending failed: '.$e->getMessage());
            Log::error('Stack trace: '.$e->getTraceAsString());
        }
    }
}
