<?php

namespace App\Enums;

use App\Enums\Traits\Selectable;

enum Meal: string
{
    use Selectable;
    case Breakfast = 'breakfast';
    case Lunch = 'lunch';
    case Dinner = 'dinner';

}
