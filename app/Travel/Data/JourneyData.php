<?php

namespace App\Travel\Data;

use App\Travel\Models\Journey;
use App\Travel\Models\Leg;
use Carbon\CarbonInterface;

readonly class JourneyData
{
    /**
     * @param  string|null  $from  The city the journey starts in, from its first station.
     * @param  string|null  $to  The city the journey ends in, from its last station.
     * @param  list<LegData>  $legs
     */
    public function __construct(
        public string $label,
        public ?CarbonInterface $date,
        public array $legs,
        public ?string $from = null,
        public ?string $to = null,
    ) {}

    public static function fromModel(Journey $journey): self
    {
        return new self(
            label: $journey->label,
            date: $journey->date,
            legs: $journey->legs->map(fn (Leg $leg) => LegData::fromModel($leg, $journey->date))->all(),
            from: $journey->legs->first(fn (Leg $leg) => $leg->fromStation !== null)?->fromStation?->city->name,
            to: $journey->legs->last(fn (Leg $leg) => $leg->toStation !== null)?->toStation?->city->name,
        );
    }

    public function departsAt(): ?CarbonInterface
    {
        foreach ($this->legs as $leg) {
            if ($leg->departureAt !== null) {
                return $leg->departureAt;
            }
        }

        return null;
    }

    public function arrivesAt(): ?CarbonInterface
    {
        foreach (array_reverse($this->legs) as $leg) {
            if ($leg->arrivalAt !== null) {
                return $leg->arrivalAt;
            }
        }

        return null;
    }

    /**
     * How often the travelers change: once between each two legs with a timetable. A metro across Paris between
     * two trains has no timetable, so it counts as part of the change, not as a ride of its own.
     */
    public function changes(): int
    {
        $rides = count(array_filter($this->legs, fn (LegData $leg) => $leg->departureAt !== null));

        return max(0, $rides - 1);
    }
}
