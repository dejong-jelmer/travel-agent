<?php

namespace App\Listeners;

use App\Events\BookingFailed;
use App\Mail\AdminBookingFailedMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotifyAdminOfFailedBooking
{
    public function handle(BookingFailed $event): void
    {
        $address = config('booking.mail');

        try {
            Mail::to($address)->queue(new AdminBookingFailedMail($event));
        } catch (\Throwable $e) {
            Log::error('Admin booking failed notification mail could not be sent: '.$e->getMessage());
        }
    }
}
