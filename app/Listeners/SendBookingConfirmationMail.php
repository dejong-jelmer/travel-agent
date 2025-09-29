<?php

namespace App\Listeners;

use App\Events\BookingCreated;
use Illuminate\Support\Facades\Log;

class SendBookingConfirmationMail
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
        Log::debug('New booking created: '.$event->booking->product->name);

    }
}
