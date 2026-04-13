<?php

namespace App\Listeners;

use App\Events\BookingFailed;
use App\Mail\AdminBookingFailedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotifyAdminOfFailedBooking implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(BookingFailed $event): void
    {
        $address = config('booking.mail');

        try {
            Mail::to($address)->send(new AdminBookingFailedMail($event));
        } catch (\Throwable $e) {
            Log::error('Admin booking failed notification mail could not be sent', [
                'admin_email' => $address,
                'exception' => $e,
            ]);
        }
    }
}
