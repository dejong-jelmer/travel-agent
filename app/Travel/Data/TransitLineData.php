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
     * Takes a line loaded through a leg: the direction lives on the pivot, and the leg gives the values of the
     * variables in the information link.
     *
     * @param  array<string, string|null>  $variables
     */
    public static function fromModel(TransitLine $line, array $variables = []): self
    {
        return new self(
            mode: $line->mode,
            name: $line->name,
            color: $line->color,
            infoLink: $line->info_link === null ? null : InfoLink::fill($line->info_link, $variables),
            direction: $line->pivot->direction,
        );
    }
}
