<?php

namespace App\Enums\Trip;

use App\Enums\Traits\HasTranslatableLabel;
use App\Enums\Traits\Selectable;

/**
 * The state of a departure. It is never stored: Departure::status() derives it from the departure's dates and
 * bookings, so it cannot drift from them.
 *
 * A departure can be full and guaranteed at once. The status then says Full, the more urgent of the two when
 * selling places; Departure::isGuaranteed() still tells whether it goes ahead.
 */
enum DepartureStatus: string
{
    use HasTranslatableLabel,
        Selectable;

    case Open = 'open';
    case Guaranteed = 'guaranteed';
    case Full = 'full';
    case Cancelled = 'cancelled';
    case Completed = 'completed';

    protected function getLabelKey(): string
    {
        return 'enum.departure_statuses';
    }
}
