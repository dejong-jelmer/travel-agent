<?php

namespace App\Travel\Data;

use App\Travel\Enums\TransitMode;
use App\Travel\Models\TransitLine;

readonly class TransitLineData
{
    public function __construct(
        public TransitMode $mode,
        public string $name,
        public ?string $color,
        public ?string $infoLink,
        public ?string $direction,
    ) {}

    /**
     * Takes a line loaded through a leg: the direction lives on the pivot.
     */
    public static function fromModel(TransitLine $line): self
    {
        return new self(
            mode: $line->mode,
            name: $line->name,
            color: $line->color,
            infoLink: $line->info_link,
            direction: $line->pivot->direction,
        );
    }
}
