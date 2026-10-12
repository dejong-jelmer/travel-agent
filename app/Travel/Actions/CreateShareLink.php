<?php

namespace App\Travel\Actions;

use App\Models\Booking;
use App\Travel\Models\ShareLink;
use Illuminate\Support\Str;

class CreateShareLink
{
    /**
     * Give the booking a share link with a new token. A link it already had stops working.
     */
    public function handle(Booking $booking): ShareLink
    {
        return ShareLink::updateOrCreate(['booking_id' => $booking->id], ['token' => Str::random(40)]);
    }
}
