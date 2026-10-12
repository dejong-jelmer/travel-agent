<?php

namespace App\Travel\Data;

use App\Travel\Models\Journey;
use App\Travel\Models\Leg;
use Carbon\CarbonInterface;

readonly class JourneyData
{
    /**
     * @param  list<LegData>  $legs
     */
    public function __construct(
        public string $label,
        public ?CarbonInterface $date,
        public array $legs,
    ) {}

    public static function fromModel(Journey $journey): self
    {
        return new self(
            label: $journey->label,
            date: $journey->date,
            legs: $journey->legs->map(fn (Leg $leg) => LegData::fromModel($leg))->all(),
        );
    }
}
