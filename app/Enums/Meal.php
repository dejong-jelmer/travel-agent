<?php

namespace App\Enums;

use App\Enums\Traits\Selectable;

enum Meal: string
{
    use Selectable;
    case Breakfast = 'Ontbijt';
    case Lunch = 'Lunch';
    case Dinner = 'Dinner';

}
