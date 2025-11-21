<?php

namespace App\Listeners;

use App\Events\BookingCreated;
use App\Mail\BookingConfirmationMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendBookingConfirmationEmail
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
    public function handle(BookingCreated $event): void
    {
        // Load relationships needed for the email
        $event->booking->load(['trip', 'mainBooker', 'travelers', 'contact']);

        try {
            // Send confirmation email to the main booker
            Mail::to($event->booking->contact->email)
                ->send(new BookingConfirmationMail($event->booking));
        } catch (\Throwable $e) {
            Log::error('Booking confirmation mail failed: '.$e->getMessage(), [
                'booking_id' => $event->booking->id,
                'booking_reference' => $event->booking->reference,
                'contact_email' => $event->booking->contact->email,
            ]);
            Log::error('Stack trace: '.$e->getTraceAsString());
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(BookingCreated $event, \Throwable $exception): void
    {
        Log::critical('Booking confirmation email permanently failed after retries', [
            'booking_id' => $event->booking->id,
            'booking_reference' => $event->booking->reference,
            'contact_email' => $event->booking->contact->email ?? 'unknown',
            'error' => $exception->getMessage(),
        ]);
    }
}
