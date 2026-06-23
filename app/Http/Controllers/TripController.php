<?php

namespace App\Http\Controllers;

use App\Enums\Destination\TravelInfo;
use App\Enums\Trip\PracticalInfo;
use App\Http\Controllers\Traits\HasPageMetadata;
use App\Models\Trip;
use App\Services\CountryService;
use App\Services\TripItemService;
use Inertia\Inertia;
use Inertia\Response;

class TripController extends Controller
{
    use HasPageMetadata;

    public function __construct(private readonly CountryService $countryService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $trips = Trip::with(['heroImage', 'prices'])->published()->get();
        $seo = $this->shareSeo('home.trips_seo');

        return Inertia::render('Trip/Index', [
            'title' => $seo['title'],
            'trips' => $trips,
            'countries' => $this->countryService->getCountriesForTrips($trips),
            'seo' => $seo,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Trip $trip): Response
    {
        $trip->load(['heroImage', 'images', 'destinations', 'itineraries', 'itineraries.image', 'items']);

        $seo = $this->shareSeo('trip.show', [
            'title' => $trip->meta_title.' | '.config('app.name'),
            'description' => $trip->meta_description,
            'og_image' => $trip->og_image_url,
        ], $this->tripJsonLd($trip));

        return Inertia::render('Trip/Show', [
            'title' => $seo['title'],
            'trip' => $trip,
            'tripItems' => TripItemService::aggregate($trip),
            'practicalSections' => PracticalInfo::labels(),
            'travelInfoSections' => TravelInfo::labels(),
            'seo' => $seo,
        ]);
    }

    /**
     * Build a schema.org TouristTrip JSON-LD object from the trip's real model fields.
     *
     * The canonical price lives in the `starting_from_price` accessor (lowest
     * `base_price_pp` across price rows, stored in cents) — the same source that
     * feeds `price_formatted`. Trips without a price are "expected" and get no Offer.
     *
     * @return array<string, mixed>
     */
    private function tripJsonLd(Trip $trip): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'TouristTrip',
            'name' => $trip->name,
            'description' => $trip->meta_description,
            'image' => $trip->og_image_url,
            'provider' => [
                '@type' => 'TravelAgency',
                'name' => config('app.name'),
                'url' => config('app.url'),
            ],
        ];

        if (! $trip->is_expected) {
            $schema['offers'] = [
                '@type' => 'Offer',
                'price' => number_format((float) $trip->starting_from_price / 100, 2, '.', ''),
                'priceCurrency' => 'EUR',
                'availability' => 'https://schema.org/InStock',
                'url' => route('trips.show', $trip->slug),
            ];
        }

        return $schema;
    }
}
