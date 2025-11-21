<?php

namespace App\Helpers;

class Breadcrumbs
{
    public const TRIP_LABEL = ['label' => 'trip.title_index'];

    public const TRIP_ROUTE = ['route' => 'admin.trips.index'];

    public const DASH_LABEL = ['label' => 'dashboard.title'];

    public const DASH_ROUTE = ['route' => 'admin.dashboard'];

    public const COUNT_LABEL = ['label' => 'country.title_index'];

    public const COUNT_ROUTE = ['route' => 'admin.countries.index'];

    public const BOOKING_LABEL = ['label' => 'booking.title_index'];

    public const BOOKING_ROUTE = ['route' => 'admin.bookings.index'];

    public static function generate(): array
    {
        $routeName = request()->route()?->getName();

        return match ($routeName) {
            // Trips
            'admin.trips.index' => [
                self::dashboardCrumb(),
                [...self::translateLabel(self::TRIP_LABEL), 'route' => null],
            ],

            'admin.trips.show' => self::tripShow(),

            'admin.trips.create' => [
                self::dashboardCrumb(),
                self::tripCrumb(),
                ['label' => __('trip.title_create'), 'route' => null],
            ],

            'admin.trips.edit' => self::tripEdit(),

            // Itineraries (nested under trip)
            'admin.trips.itineraries.index' => self::tripItinerariesIndex(),

            'admin.trips.itineraries.create' => [
                ...self::tripItinerariesIndex(),
                ['label' => __('itinerary.title_create'), 'route' => null],
            ],

            // Itinerary edit (unnested route)
            'admin.itineraries.edit' => self::itineraryEdit(),

            // Booking
            'admin.bookings.index' => [
                self::dashboardCrumb(),
                [...self::translateLabel(self::BOOKING_LABEL), 'route' => null],
            ],

            'admin.bookings.show' => self::bookingShow(),
            'admin.bookings.edit' => self::bookingEdit(),

            // Countries
            'admin.countries.index' => [
                self::dashboardCrumb(),
                [...self::translateLabel(self::COUNT_LABEL), 'route' => null],
            ],
            'admin.countries.create' => [
                self::dashboardCrumb(),
                [...self::translateLabel(self::COUNT_LABEL), ...self::COUNT_ROUTE],
                ['label' => __('country.title_create'), 'route' => null],
            ],

            default => [],
        };
    }

    protected static function tripShow(): array
    {
        $trip = request()->route('trip');

        return [
            self::dashboardCrumb(),
            self::tripCrumb(),
            ['label' => $trip?->name ?? __('trip.title_show'), 'route' => null],
        ];
    }

    protected static function tripEdit(): array
    {
        $trip = request()->route('trip');

        return [
            self::dashboardCrumb(),
            self::tripCrumb(),
            ['label' => $trip?->name ?? __('trip.title_show'), 'route' => 'admin.trips.show', 'params' => [$trip]],
            ['label' => __('trip.title_edit'), 'route' => null],
        ];
    }

    protected static function bookingShow(): array
    {
        return [
            self::dashboardCrumb(),
            [...self::translateLabel(self::BOOKING_LABEL), ...self::BOOKING_ROUTE],
            ['label' => __('booking.title_show'), 'route' => null],
        ];
    }

    protected static function bookingEdit(): array
    {
        $booking = request()->route('booking');

        return [
            self::dashboardCrumb(),
            [...self::translateLabel(self::BOOKING_LABEL), ...self::BOOKING_ROUTE],
            ['label' => __('booking.title_show'), 'route' => 'admin.bookings.show', 'params' => [$booking]],
            ['label' => __('booking.title_edit'), 'route' => null],
        ];
    }

    protected static function tripItinerariesIndex(): array
    {
        $trip = request()->route('trip');

        return [
            self::dashboardCrumb(),
            self::tripCrumb(),
            ['label' => $trip?->name ?? __('trip.title_show'), 'route' => 'admin.trips.show', 'params' => [$trip]],
            ['label' => __('itinerary.title_index'), 'route' => null],
        ];
    }

    protected static function itineraryEdit(): array
    {
        $itinerary = request()->route('itinerary');
        $trip = $itinerary?->trip;

        return [
            self::dashboardCrumb(),
            self::tripCrumb(),
            ['label' => $trip?->name ?? __('trip.title_show'), 'route' => 'admin.trips.show', 'params' => [$trip]],
            ['label' => __('itinerary.title_index'), 'route' => 'admin.trips.itineraries.index', 'params' => [$trip]],
            ['label' => __('itinerary.title_edit'), 'route' => null],
        ];
    }

    private static function dashboardCrumb(): array
    {
        return [...self::translateLabel(self::DASH_LABEL), ...self::DASH_ROUTE];
    }

    private static function tripCrumb(): array
    {
        return [...self::translateLabel(self::TRIP_LABEL), ...self::TRIP_ROUTE];
    }

    private static function translateLabel(array $label): array
    {
        if (isset($label['label'])) {
            $label['label'] = __($label['label']);
        }

        return $label;
    }
}
