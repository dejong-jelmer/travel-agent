<?php

namespace App\Travel\Actions;

use App\Models\Booking;
use App\Travel\Models\ShareLink;

class DeleteShareLink
{
    /**
     * Take the booking's share link away, so its token no longer opens the documents.
     */
    public function handle(Booking $booking): void
    {
        ShareLink::where('booking_id', $booking->id)->delete();
    }
}
