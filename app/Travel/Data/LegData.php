<?php

namespace App\Travel\Data;

use App\Travel\Enums\LegType;
use App\Travel\Models\Leg;
use App\Travel\Models\TransitLine;
use Carbon\CarbonInterface;

readonly class LegData
{
    /**
     * @param  int|null  $durationMinutes  Leg::duration: from the times when both are set, otherwise the stored duration.
     * @param  list<TransitLineData>  $transitLines
     */
    public function __construct(
        public LegType $type,
        public ?string $fromStation,
        public ?string $toStation,
        public ?CarbonInterface $departureAt,
        public ?CarbonInterface $arrivalAt,
        public ?int $durationMinutes,
        public ?string $number,
        public ?string $carriage,
        public ?string $seat,
        public ?string $note,
        public array $transitLines,
    ) {}

    public static function fromModel(Leg $leg): self
    {
        return new self(
            type: $leg->type,
            fromStation: $leg->fromStation?->name,
            toStation: $leg->toStation?->name,
            departureAt: $leg->departure_at,
            arrivalAt: $leg->arrival_at,
            durationMinutes: $leg->duration,
            number: $leg->number,
            carriage: $leg->carriage,
            seat: $leg->seat,
            note: $leg->note,
            transitLines: $leg->transitLines->map(fn (TransitLine $line) => TransitLineData::fromModel($line))->all(),
        );
    }
}
