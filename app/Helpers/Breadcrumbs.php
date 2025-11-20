<?php

namespace App\Helpers;

class Breadcrumbs
{
    const TRIP_LABEL = ['label' => 'Reizen'];

    const TRIP_ROUTE = ['route' => 'admin.trips.index'];

    const DASH_LABEL = ['label' => 'Dashboard'];

    const DASH_ROUTE = ['route' => 'admin.dashboard'];

    const COUNT_LABEL = ['label' => 'Landen'];

    const COUNT_ROUTE = ['route' => 'admin.countries.index'];

    public static function generate(): array
    {
        $routeName = request()->route()?->getName();

        return match ($routeName) {
            // Trips
            'admin.trips.index' => [
                [...self::DASH_LABEL, ...self::DASH_ROUTE],
                [...self::TRIP_LABEL, 'route' => null],
            ],

            'admin.trips.show' => self::tripShow(),

            'admin.trips.create' => [
                [...self::TRIP_LABEL, ...self::TRIP_ROUTE],
                [...self::TRIP_LABEL, 'route' => null],
            ],

            'admin.trips.edit' => self::tripEdit(),

            'admin.trips.update' => self::tripEdit(),

            // Itineraries (nested under trip)
            'admin.trips.itineraries.index' => self::tripItinerariesIndex(),

            'admin.trips.itineraries.create' => [
                ...self::tripItinerariesIndex(),
                ['label' => 'Nieuwe dag', 'route' => null],
            ],

            // Itinerary edit (losse route)
            'admin.itineraries.edit' => self::itineraryEdit(),
            'admin.itineraries.update' => self::itineraryEdit(),

            // Countries
            'admin.countries.index' => [
                [...self::DASH_LABEL, ...self::DASH_ROUTE],
                [...self::COUNT_LABEL, 'route' => null],
            ],
            'admin.countries.create' => [
                [...self::DASH_LABEL, ...self::DASH_ROUTE],
                [...self::COUNT_LABEL, ...self::COUNT_ROUTE],
                ['label' => 'Land aanmaken', 'route' => null],
            ],

            default => [],
        };
    }

    protected static function tripShow(): array
    {
        $trip = request()->route('trip');

        return [
            [...self::DASH_LABEL, ...self::DASH_ROUTE],
            [...self::TRIP_LABEL, ...self::TRIP_ROUTE],
            ['label' => $trip?->name ?? 'Reis', 'route' => null],
        ];
    }

    protected static function tripEdit(): array
    {
        $trip = request()->route('trip');

        return [
            [...self::DASH_LABEL, ...self::DASH_ROUTE],
            [...self::TRIP_LABEL, ...self::TRIP_ROUTE],
            ['label' => $trip?->name ?? 'Bewerken', 'route' => null],
        ];
    }

    protected static function tripItinerariesIndex(): array
    {
        $trip = request()->route('trip');

        return [
            [...self::DASH_LABEL, ...self::DASH_ROUTE],
            [...self::TRIP_LABEL, ...self::TRIP_ROUTE],
            ['label' => $trip?->name ?? 'Reis', 'route' => 'admin.trips.edit', 'params' => [$trip]],
            ['label' => 'Reisdagen', 'route' => null],
        ];
    }

    protected static function itineraryEdit(): array
    {
        $itinerary = request()->route('itinerary');
        $trip = $itinerary?->trip;

        return [
            [...self::DASH_LABEL, ...self::DASH_ROUTE],
            [...self::TRIP_LABEL, ...self::TRIP_ROUTE],
            ['label' => $trip?->name ?? 'Reis', 'route' => 'admin.trips.edit', 'params' => [$trip]],
            ['label' => 'Reisdagen', 'route' => 'admin.trips.itineraries.index', 'params' => [$trip]],
            ['label' => 'Bewerk reisdag', 'route' => null],
        ];
    }
}
