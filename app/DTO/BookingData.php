<?php

namespace App\DTO;

use App\DTO\Traits\ArrayableDTO;
use App\Http\Requests\BookingRequest;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;

class BookingData implements Arrayable
{
    use ArrayableDTO;

    public function __construct(
        public readonly Product $trip,
        public readonly int $main_booker,
        public readonly BookingContactData $contact,
        public readonly Carbon $date,
        public readonly array $adult,
        public readonly array $child,
        public readonly Bool $confirmed,
    ) {}

    public static function fromRequest(BookingRequest $request): self
    {
        $validated = $request->validated();
        $travelers = $validated['travelers'];
        $trip = Product::find($validated['trip']['id']);
        $mainBooker = $travelers['adults'][$validated['main_booker']];

        return new self(
            trip: $trip,
            adult: BookingTravelerData::manyFromArray($travelers['adults'] ?? []),
            main_booker: $validated['main_booker'],
            child: BookingTravelerData::manyFromArray($travelers['children'] ?? []),
            contact: BookingContactData::fromArray($mainBooker['full_name'], $validated['contact']),
            date: Carbon::parse($validated['departure_date']),
            confirmed: $validated['confirmed']
        );
    }
}
