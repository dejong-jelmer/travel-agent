<?php

namespace App\Listeners;

use App\Events\BookingCreated;
use App\Mail\BookingMail;
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
        try {
            Mail::to($address)->send(
                new BookingMail($event->booking)
            );
        } catch (\Throwable $e) {
            Log::error('Mail sending failed: '.$e->getMessage());
            Log::error('Stack trace: '.$e->getTraceAsString());
        }
    }
}
