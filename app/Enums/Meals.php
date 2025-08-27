<?php
namespace App\Enums;

use App\Enums\Traits\Selectable;

enum Meals: string
{
    use Selectable;
    case BREAKFAST = 'Ontbijt';
    case LUNCH = 'Lunch';
    case DINNER = 'Dinner';

}
