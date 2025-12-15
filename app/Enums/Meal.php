<?php

namespace App\Enums;

use App\Enums\Traits\Selectable;

enum Meal: string
{
    use Selectable;
    case Breakfast = 'breakfast';
    case Lunch = 'lunch';
    case Dinner = 'dinner';

    public function label(): string
    {
        return match ($this) {
            Meal::Breakfast => __('itinerary.meals.breakfast'),
            Meal::Lunch => __('itinerary.meals.lunch'),
            Meal::Dinner => __('itinerary.meals.dinner'),
        };
    }
}
