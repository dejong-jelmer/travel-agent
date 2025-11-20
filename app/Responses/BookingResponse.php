<?php

namespace App\Responses;

use App\Enums\ModelAction;
use App\Models\Booking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use InvalidArgumentException;

class BookingResponse
{
    public function __construct(protected Booking $booking, private array $options = []) {}

    /**
     * Undocumented function
     *
     * @throws InvalidArgumentException
     */
    public function toResponse(ModelAction $action): RedirectResponse|JsonResponse
    {
        if (request()->expectsJson() && request()->is('api/*')) {
            return new JsonResponse([
                'message' => "Success: Booking {$action->value}",
                'booking' => $this->booking->load(['travelers', 'contact']),
            ], 200);
        }

        // Web redirect
        return match ($action) {
            ModelAction::Created => redirect()
                ->route('bookings.confirmation', ['booking' => $this->booking])
                ->with('success', __('booking.stored')),
            ModelAction::Updated => redirect()
                ->route('admin.bookings.index')
                ->with('success', __('booking.updated', ['reference' => $this->booking->reference])),
            default => throw new InvalidArgumentException("Unknown action: {$action->value}")
        };

    }

    public static function make(Booking $booking): static
    {
        return new static($booking);
    }
}
