<?php

namespace App\Enums\Trip;

use App\Enums\Traits\HasTranslatableLabel;
use App\Enums\Traits\Selectable;

/**
 * How a trip is sold: on a date the customer picks, or on fixed departures shared by a group.
 */
enum TripFormat: string
{
    use HasTranslatableLabel,
        Selectable;

    case Individual = 'individual';
    case Group = 'group';

    protected function getLabelKey(): string
    {
        return 'enum.trip_formats';
    }
}
