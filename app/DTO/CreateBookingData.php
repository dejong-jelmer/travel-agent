<?php

namespace App\DTO;

use App\DTO\Traits\ArrayableDTO;
use App\DTO\Traits\BookingDataParser;
use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

class CreateBookingData implements Arrayable
{
    use ArrayableDTO, BookingDataParser;

    public function __construct(
        public readonly Trip $trip,
        public readonly array $main_booker,
        public readonly array $travelers,
        public readonly BookingContactData $contact,
        public readonly Carbon $date,
        public readonly bool $has_accepted_conditions,
        public readonly bool $has_confirmed
    ) {}

    /**
     * Create from validated request
     */
    public static function fromRequest(Request $request): self
    {
        $validated = $request->validated();
        $parsed = self::parseValidatedData($validated);

        return new self(
            trip: self::findTrip($validated['trip']['id'] ?? null),
            main_booker: $parsed['main_booker'],
            travelers: $parsed['travelers'],
            contact: $parsed['contact'],
            date: Carbon::parse($validated['departure_date']),
            has_accepted_conditions: $validated['has_accepted_conditions'] ?? false,
            has_confirmed: $validated['has_confirmed'] ?? false,
        );
    }
}
