<?php

namespace App\Enums\Trip;

use App\Enums\Traits\HasTranslatableLabel;

enum PracticalInfo: string
{
    use HasTranslatableLabel;

    case TravelPeriod = 'travel_period';
    case DepartureDates = 'departure_dates';
    case OutboundReturn = 'outbound_return';
    case Transport = 'transport';
    case Accommodation = 'accommodation';
    case Additional = 'additional';

    protected function getLabelKey(): string
    {
        return 'trip.practical-info.sections';
    }
}
