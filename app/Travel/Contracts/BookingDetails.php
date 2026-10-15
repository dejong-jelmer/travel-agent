<?php

namespace App\Travel\Contracts;

use App\Models\Booking;

/**
 * What the documents need from outside the module: the trip and the travelers of a booking, and the number to call.
 * The app binds an implementation, so the module never reaches into Trip, BookingTraveler or the app's config itself.
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

    /**
     * The number the travelers of the booking call when they are stuck on the way, or null when there is none.
     */
    public function emergencyPhone(Booking $booking): ?string;
}
