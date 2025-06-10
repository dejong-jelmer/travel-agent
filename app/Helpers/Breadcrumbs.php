<?php

namespace App\Helpers;

class Breadcrumbs
{
    const PROD_LABEL = ['label' => 'Reis producten'];

    const PROD_ROUTE = ['route' => 'products.index'];

    const DASH_LABEL = ['label' => 'Dashboard'];

    const DASH_ROUTE = ['route' => 'admin.dashboard'];

    const COUNT_LABEL = ['label' => 'Landen'];

    const COUNT_ROUTE = ['route' => 'countries.index'];

    public static function generate(): array
    {
        $routeName = request()->route()?->getName();

        return match ($routeName) {
            // Products
            'products.index' => [
                [...self::DASH_LABEL, ...self::DASH_ROUTE],
                [...self::PROD_LABEL, 'route' => null],
            ],

            'products.show' => self::productShow(),

            'products.create' => [
                [...self::PROD_LABEL, ...self::PROD_ROUTE],
                [...self::PROD_LABEL, 'route' => null],
            ],

            'products.edit' => self::productEdit(),

            'products.update' => self::productEdit(),

            // Itineraries (nested onder product)
            'products.itineraries.index' => self::productItinerariesIndex(),

            'products.itineraries.create' => [
                ...self::productItinerariesIndex(),
                ['label' => 'Nieuwe dag', 'route' => null],
            ],

            // Itinerary edit (losse route)
            'itineraries.edit' => self::itineraryEdit(),
            'itineraries.update' => self::itineraryEdit(),

            // Countries
            'countries.index' => [
                [...self::DASH_LABEL, ...self::DASH_ROUTE],
                [...self::COUNT_LABEL, 'route' => null],
            ],
            'countries.create' => [
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
            ['label' => $product?->name ?? 'Reis product', 'route' => 'products.edit', 'params' => [$product]],
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
            ['label' => $product?->name ?? 'Reis', 'route' => 'products.edit', 'params' => [$product]],
            ['label' => 'Reisdagen', 'route' => 'products.itineraries.index', 'params' => [$product]],
            ['label' => 'Bewerk reisdag', 'route' => null],
        ];
    }
}
