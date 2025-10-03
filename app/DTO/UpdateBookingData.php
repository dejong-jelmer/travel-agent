<?php

namespace App\DTO;

use App\DTO\BookingContactData;
use App\DTO\BookingTravelerData;
use App\DTO\Traits\ArrayableDTO;
use App\DTO\Traits\BookingDataParser;
use App\Exceptions\ModelNotFoundException;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

class UpdateBookingData implements Arrayable
{
    use ArrayableDTO, BookingDataParser;

    public function __construct(
        public readonly array $main_booker,
        public readonly array $travelers,
        public readonly BookingContactData $contact,
    ) {}

    /**
     * Create from validated request
     */
    public static function fromRequest(Request $request): self
    {
        $validated = $request->validated();
        $parsed = self::parseValidatedData($validated);

        return new self(
            main_booker: $parsed['main_booker'],
            travelers: $parsed['travelers'],
            contact: $parsed['contact'],
        );
    }
}
