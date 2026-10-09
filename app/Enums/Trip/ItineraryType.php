<?php

namespace App\Enums\Trip;

use App\Enums\Traits\HasTranslatableLabel;
use App\Enums\Traits\Selectable;

/**
 * The kind of day an itinerary item covers: a day spent travelling by (night) train, or a day at the destination.
 */
enum ItineraryType: string
{
    use HasTranslatableLabel,
        Selectable;

    case NightTrain = 'night_train';
    case Train = 'train';
    case Stay = 'stay';

    protected function getLabelKey(): string
    {
        return 'itinerary.type';
    }
}
