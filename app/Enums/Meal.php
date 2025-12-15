<?php

namespace App\Enums;

use App\Enums\Traits\HasTranslatableLabel;
use App\Enums\Traits\Selectable;

enum Meal: string
{
    use HasTranslatableLabel,
        Selectable;

    private const LABEL_KEY = 'itinerary.meals';

    case Breakfast = 'breakfast';
    case Lunch = 'lunch';
    case Dinner = 'dinner';
}
