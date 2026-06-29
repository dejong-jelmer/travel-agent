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
        $seo = $this->shareSeo('seo.trips');

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

        $seo = $this->shareSeo(overrides: [
            'title' => $trip->meta_title.' | '.config('app.name'),
            'description' => $trip->meta_description,
            'og_image' => $trip->og_image_url,
        ], jsonLd: $trip->toTouristTripSchema());

        return Inertia::render('Trip/Show', [
            'title' => $seo['title'],
            'trip' => $trip,
            'tripItems' => TripItemService::aggregate($trip),
            'practicalSections' => PracticalInfo::labels(),
            'travelInfoSections' => TravelInfo::labels(),
            'seo' => $seo,
        ]);
    }
}
