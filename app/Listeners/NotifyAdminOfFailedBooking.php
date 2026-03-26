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
<<<<<<< HEAD
            Mail::to($address)->queue(new AdminBookingFailedMail($event));
=======
            Mail::to($address)->send(new AdminBookingFailedMail($event));
>>>>>>> b9e884b3fa401a1a668de43a24b0f92b9502b33e
        } catch (\Throwable $e) {
            Log::error('Admin booking failed notification mail could not be sent: '.$e->getMessage());
        }
    }
}
