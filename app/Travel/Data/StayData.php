<?php

namespace App\Travel\Data;

use App\Travel\Models\Stay;
use Carbon\CarbonInterface;

readonly class StayData
{
    public function __construct(
        public ?CarbonInterface $arrival,
        public ?CarbonInterface $departure,
        public ?int $nights,
        public AccommodationData $accommodation,
    ) {}

    public static function fromModel(Stay $stay): self
    {
        return new self(
            arrival: $stay->arrival,
            departure: $stay->departure,
            nights: $stay->nights,
            accommodation: AccommodationData::fromModel($stay->accommodation),
        );
    }
}
