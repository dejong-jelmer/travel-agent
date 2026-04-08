<?php

namespace App\Enums\Trip;

enum TripType: string
{
    case CityTrip = 'city_trip';
    case SingleBase = 'single_base';
    case Tour = 'tour';

    public function label(): string
    {
        return __("enum.trip_types.{$this->value}");
    }
}
