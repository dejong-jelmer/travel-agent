<?php

namespace App\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use App\Models\Booking;

class BookingResponse
{
    protected Booking $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function toResponse(): RedirectResponse|JsonResponse
    {
        if (request()->expectsJson() || request()->is('api/*')) {
            return new JsonResponse([
                'message' => 'Success: Booking created',
                'booking' => $this->booking->load(['travelers', 'contact']),
            ], 200);
        }

        // Web redirect
        return to_route('bookings.confirmation', ['booking' => $this->booking])
            ->with('success', __('booking.confirmed'));
    }

    public static function make(Booking $booking): static
    {
        return new static($booking);
    }
}
