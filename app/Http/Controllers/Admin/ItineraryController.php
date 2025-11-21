<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ImageRelation;
use App\Enums\Meal;
use App\Enums\Transport;
use App\Http\Requests\CreateItineraryRequest;
use App\Http\Requests\UpdateItineraryOrderRequest;
use App\Http\Requests\UpdateItineraryRequest;
use App\Models\Itinerary;
use App\Models\Trip;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class ItineraryController extends Controller
{
    private string $appName;

    public function __construct()
    {
        $this->appName = config('app.name');
    }

    /**
     * Display a listing of a trip's itinerary.
     */
    public function index(Trip $trip): Response
    {
        return Inertia::render('Admin/Trip/Itinerary/Index', [
            'trip' => $trip->load('itineraries.image'),
            'title' => __('itinerary.title_index').' - '.$this->appName,
        ]);
    }

    public function updateOrder(UpdateItineraryOrderRequest $request, Trip $trip): HttpResponse
    {
        $validated = $request->safe();
        foreach ($validated['itineraries'] as $itinerary) {
            $trip->itineraries()->where('id', $itinerary['id'])->update(['order' => $itinerary['order']]);
        }

        return response()->json([
            'success' => true,
        ], 200);
    }

    /**
     * Show the form for creating a new itinerary block for the trip.
     */
    public function create(Trip $trip): Response
    {
        return Inertia::render('Admin/Trip/Itinerary/Create', [
            'trip' => $trip,
            'meals' => Meal::options(),
            'transport' => Transport::options(),
            'title' => __('itinerary.title_create').' - '.$this->appName,
        ]);
    }

    /**
     * Store a newly created itinerary block for the trip.
     */
    public function store(CreateItineraryRequest $request, Trip $trip): RedirectResponse
    {
        $itinerary = new Itinerary;
        $validatedFields = $request->safe()->except('image');
        $validatedImage = $request->safe()->only('image');

        $itinerary->fill($validatedFields);
        $itinerary->trip()->associate($trip);
        $itinerary->order = $trip->itineraries->count() + 1;
        $itinerary->save();
        $itinerary->syncImages($validatedImage['image'], ImageRelation::Image);

        return redirect()
            ->route('admin.trips.itineraries.index', $itinerary->trip)
            ->with('success', __('itinerary.created'));
    }

    /**
     * Show the form for editing an itinerary block for the trip.
     */
    public function edit(Itinerary $itinerary): Response
    {
        return Inertia::render('Admin/Trip/Itinerary/Edit', [
            'itinerary' => $itinerary->load('image'),
            'meals' => Meal::options(),
            'transport' => Transport::options(),
            'title' => __('itinerary.title_edit').' - '.$this->appName,
        ]);
    }

    /**
     * Update an itinerary block in storage.
     */
    public function update(UpdateItineraryRequest $request, Itinerary $itinerary): RedirectResponse
    {
        $validatedFields = $request->safe()->except('image');
        $validatedImage = $request->safe()->only('image');

        $itinerary->update($validatedFields);

        // Sync image (handles both existing path and new upload)
        if (isset($validatedImage['image'])) {
            $itinerary->syncImages($validatedImage['image'], ImageRelation::Image);
        }

        return redirect()
            ->route('admin.trips.itineraries.index', $itinerary->trip)
            ->with('success', __('itinerary.updated'));
    }

    /**
     * Remove an itinerary block from storage.
     */
    public function destroy(Itinerary $itinerary): RedirectResponse
    {
        $trip = $itinerary->trip;
        $itineraryTitle = $itinerary->title;
        Itinerary::destroy($itinerary->id);

        return redirect()
            ->route('admin.trips.itineraries.index', $trip)
            ->with('success', __('itinerary.deleted'));
    }
}
