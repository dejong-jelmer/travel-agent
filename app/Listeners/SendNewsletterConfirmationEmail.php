<?php

namespace App\Listeners;

use App\Events\NewsletterSubscriptionRequested;
use App\Mail\NewsletterConfirmation;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

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

        try {
            Mail::to(new Address(
                $subscriber->email,
                $subscriber->name ?: strtok($subscriber->email, '@')
            ))->queue(
                new NewsletterConfirmation($subscriber)
            );
        } catch (Throwable $e) {
            Log::error('Mail sending failed: '.$e->getMessage());
            Log::error('Stack trace: '.$e->getTraceAsString());
        }
    }
}
