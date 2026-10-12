<?php

namespace App\Travel\Contracts;

use App\Models\Booking;

/**
 * What the documents need from a booking that lives outside the module: the trip and its travelers. The app binds an
 * implementation, so the module never reaches into Trip or BookingTraveler itself.
 */
interface BookingDetails
{
    /**
     * The title of the trip, as the documents show it.
     */
    public function title(Booking $booking): string;

    /**
     * The full names of the travelers, the main booker first.
     *
     * @return list<string>
     */
    public function travelerNames(Booking $booking): array;
}
