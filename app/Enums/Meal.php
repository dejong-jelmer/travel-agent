<?php

namespace App\Enums;

use App\Enums\Traits\HasTranslatableLabel;
use App\Enums\Traits\Selectable;

enum Meal: string
{
    use Selectable,
        HasTranslatableLabel;

    private const LABEL_KEY = 'itinerary.meals';

    case Breakfast = 'breakfast';
    case Lunch = 'lunch';
    case Dinner = 'dinner';
}
