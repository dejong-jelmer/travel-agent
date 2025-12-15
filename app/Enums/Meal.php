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
            self::Breakfast => __('itinerary.meals.breakfast'),
            self::Lunch => __('itinerary.meals.lunch'),
            self::Dinner => __('itinerary.meals.dinner'),
        };
    }
}
