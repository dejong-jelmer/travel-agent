<?php

namespace App\Listeners;

use App\Events\BookingCreated;
use App\Mail\AdminBookingNotificationMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotifyAdminOfNewBooking
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
        $address = config('booking.mail');

        // Load relationships needed for the email
        $event->booking->load(['product.countries', 'mainBooker', 'travelers', 'contact']);

        try {
            Mail::to($address)->send(
                new AdminBookingNotificationMail($event->booking)
            );
        } catch (\Throwable $e) {
            Log::error('Admin booking notification mail failed: '.$e->getMessage(), [
                'booking_id' => $event->booking->id,
                'booking_reference' => $event->booking->reference,
                'admin_email' => $address,
            ]);
            Log::error('Stack trace: '.$e->getTraceAsString());
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(BookingCreated $event, \Throwable $exception): void
    {
        Log::critical('Admin booking notification email permanently failed after retries', [
            'booking_id' => $event->booking->id,
            'booking_reference' => $event->booking->reference,
            'admin_email' => config('booking.mail'),
            'error' => $exception->getMessage(),
        ]);
    }
}
