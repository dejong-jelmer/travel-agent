<?php

namespace App\Enums;

use App\Enums\Traits\HasTranslatableLabel;
use App\Enums\Traits\Selectable;

enum Meal: string
{
    use HasTranslatableLabel,
        Selectable;

    case Breakfast = 'breakfast';
    case Lunch = 'lunch';
    case Dinner = 'dinner';

    protected function getLabelKey(): string
    {
        return 'itinerary.meals';
    }
}
