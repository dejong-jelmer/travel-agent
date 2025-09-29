<?php

namespace App\Helpers;

class Breadcrumbs
{
    const PROD_LABEL = ['label' => 'Reis producten'];

    const PROD_ROUTE = ['route' => 'admin.products.index'];

    const DASH_LABEL = ['label' => 'Dashboard'];

    const DASH_ROUTE = ['route' => 'admin.dashboard'];

    const COUNT_LABEL = ['label' => 'Landen'];

    const COUNT_ROUTE = ['route' => 'admin.countries.index'];

    public static function generate(): array
    {
        $routeName = request()->route()?->getName();

        return match ($routeName) {
            // Products
            'admin.products.index' => [
                [...self::DASH_LABEL, ...self::DASH_ROUTE],
                [...self::PROD_LABEL, 'route' => null],
            ],

            'admin.products.show' => self::productShow(),

            'admin.products.create' => [
                [...self::PROD_LABEL, ...self::PROD_ROUTE],
                [...self::PROD_LABEL, 'route' => null],
            ],

            'admin.products.edit' => self::productEdit(),

            'admin.products.update' => self::productEdit(),

            // Itineraries (nested onder product)
            'admin.products.itineraries.index' => self::productItinerariesIndex(),

            'admin.products.itineraries.create' => [
                ...self::productItinerariesIndex(),
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

    protected static function productShow(): array
    {
        $product = request()->route('product');

        return [
            [...self::DASH_LABEL, ...self::DASH_ROUTE],
            [...self::PROD_LABEL, ...self::PROD_ROUTE],
            ['label' => $product?->name ?? 'Reis product', 'route' => null],
        ];
    }

    protected static function productEdit(): array
    {
        $product = request()->route('product');

        return [
            [...self::DASH_LABEL, ...self::DASH_ROUTE],
            [...self::PROD_LABEL, ...self::PROD_ROUTE],
            ['label' => $product?->name ?? 'Bewerken', 'route' => null],
        ];
    }

    protected static function productItinerariesIndex(): array
    {
        $product = request()->route('product');

        return [
            [...self::DASH_LABEL, ...self::DASH_ROUTE],
            [...self::PROD_LABEL, ...self::PROD_ROUTE],
            ['label' => $product?->name ?? 'Reis product', 'route' => 'admin.products.edit', 'params' => [$product]],
            ['label' => 'Reisdagen', 'route' => null],
        ];
    }

    protected static function itineraryEdit(): array
    {
        $itinerary = request()->route('itinerary');
        $product = $itinerary?->product;

        return [
            [...self::DASH_LABEL, ...self::DASH_ROUTE],
            [...self::PROD_LABEL, ...self::PROD_ROUTE],
            ['label' => $product?->name ?? 'Reis', 'route' => 'admin.products.edit', 'params' => [$product]],
            ['label' => 'Reisdagen', 'route' => 'admin.products.itineraries.index', 'params' => [$product]],
            ['label' => 'Bewerk reisdag', 'route' => null],
        ];
    }
}
