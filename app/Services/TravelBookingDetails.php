<?php

namespace App\Services;

use App\Models\Booking;
use App\Travel\Contracts\BookingDetails;

/**
 * Gives the travel module the trip title and the travelers of a booking, and the emergency number, which it may not
 * read itself.
 */
class TravelBookingDetails implements BookingDetails
{
    public function title(Booking $booking): string
    {
        return $booking->loadMissing('trip')->trip->name;
    }

    /**
     * Queried through the booking itself: travelers() puts the main booker first by the booking's main_booker_id,
     * which an eager load does not have.
     */
    public function travelerNames(Booking $booking): array
    {
        return $booking->travelers()->get()->pluck('full_name')->values()->all();
    }

    public function emergencyPhone(Booking $booking): ?string
    {
        return config('contact.emergency_phone') ?: null;
    }
}
