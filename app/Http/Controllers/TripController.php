<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\HasPageMetadata;
use App\Models\Trip;
use Inertia\Inertia;
use Inertia\Response;

class TripController extends Controller
{
    use HasPageMetadata;

    /**
     * Display the specified resource.
     */
    public function show(Trip $trip): Response
    {
        return Inertia::render('Trip/Show', [
            'title' => $this->pageTitle($trip->name),
            'trip' => $trip->load(['heroImage', 'images', 'countries', 'itineraries', 'itineraries.image']),
            'seo' => [
                'title' => $trip->meta_title,
                'description' => $trip->meta_description,
                'og_image' => $trip->og_image_url,
            ],
        ]);
    }
}
