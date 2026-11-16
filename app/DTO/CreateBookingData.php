<?php

namespace App\DTO;

use App\DTO\Traits\ArrayableDTO;
use App\DTO\Traits\BookingDataParser;
use App\Models\Departure;
use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Foundation\Http\FormRequest;

class CreateBookingData implements Arrayable
{
    use ArrayableDTO, BookingDataParser;

    public function __construct(
        public readonly Trip $trip,
        public readonly ?Departure $departure,
        public readonly array $main_booker,
        public readonly array $travelers,
        public readonly BookingContactData $contact,
        public readonly Carbon $date,
        public readonly ?Carbon $return_date,
        public readonly bool $has_accepted_conditions,
        public readonly bool $has_confirmed,
        public readonly array $cost_items,
        public readonly ?int $margin_basis_points,
        public readonly ?int $margin_amount,
        public readonly ?int $fee_per_person,
        public readonly ?int $final_price,
    ) {}

    /**
     * Create from validated request
     */
    public static function fromRequest(FormRequest $request): self
    {
        $validated = $request->validated();
        $parsed = self::parseValidatedData($validated);

        // A booking of a group trip takes its dates from the departure it is made on
        $departure = isset($validated['departure_id']) ? Departure::findOrFail($validated['departure_id']) : null;

        return new self(
            trip: self::findTrip($validated['trip']['id'] ?? null),
            departure: $departure,
            main_booker: $parsed['main_booker'],
            travelers: $parsed['travelers'],
            contact: $parsed['contact'],
            date: $departure ? Carbon::instance($departure->departs_on) : Carbon::parse($validated['departure_date']),
            return_date: match (true) {
                $departure !== null => Carbon::instance($departure->returns_on),
                isset($validated['return_date']) => Carbon::parse($validated['return_date']),
                default => null,
            },
            has_accepted_conditions: $validated['has_accepted_conditions'] ?? false,
            has_confirmed: $validated['has_confirmed'] ?? false,
            cost_items: $parsed['cost_items'],
            margin_basis_points: $parsed['margin_basis_points'],
            margin_amount: $parsed['margin_amount'],
            fee_per_person: $parsed['fee_per_person'],
            final_price: $parsed['final_price'],
        );
    }
}
