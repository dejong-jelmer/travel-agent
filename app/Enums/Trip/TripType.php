<?php

namespace App\Enums\Trip;

use App\Enums\Traits\Selectable;

/**
 * What kind of trip it is: one city, one base to explore the area from, or a tour along several places.
 */
enum TripType: string
{
    use Selectable;

    case CityTrip = 'city_trip';
    case SingleBase = 'single_base';
    case Tour = 'tour';

    public function label(): string
    {
        return __("enum.trip_types.{$this->value}");
    }

    /**
     * One sentence for visitors on what this kind of trip means, shown on the trip page.
     */
    public function description(): string
    {
        return __("enum.trip_type_descriptions.{$this->value}");
    }

    /**
     * @return array{value: string, label: string, description: string}
     */
    public function toDisplay(): array
    {
        return [
            'value' => $this->value,
            'label' => $this->label(),
            'description' => $this->description(),
        ];
    }
}
