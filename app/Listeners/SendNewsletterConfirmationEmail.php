<?php

namespace App\Listeners;

use App\Events\NewsletterSubscriptionRequested;
use App\Mail\NewsletterConfirmation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendNewsletterConfirmationEmail implements ShouldQueue
{
    use InteractsWithQueue;

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
            ))->send(
                new NewsletterConfirmation($subscriber)
            );
        } catch (Throwable $e) {
            Log::error('Newsletter confirmation mail failed', [
                'subscriber_id' => $subscriber->id,
                'subscriber_email' => $subscriber->email,
                'exception' => $e,
            ]);
        }
    }
}
