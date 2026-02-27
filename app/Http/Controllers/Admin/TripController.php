<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ImageRelation;
use App\Enums\Transport;
use App\Enums\Trip\ItemCategory;
use App\Enums\Trip\ItemType;
use App\Enums\Trip\PracticalInfo;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasPageMetadata;
use App\Http\Requests\CreateTripRequest;
use App\Http\Requests\DataTableRequest;
use App\Http\Requests\UpdateTripRequest;
use App\Models\Destination;
use App\Models\Trip;
use App\Services\DataTableService;
use App\Services\TripItemService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class TripController extends Controller
{
    use HasPageMetadata;

    public function __construct(
        private DataTableService $dataTableService,
        private TripItemService $tripItemService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(DataTableRequest $request): Response
    {
        $trips = $this->dataTableService
            ->applyFilters(Trip::with(['destinations', 'itineraries', 'heroImage']), $request, Trip::filters())
            ->paginate()
            ->withQueryString();

        return Inertia::render('Admin/Trip/Index', [
            'trips' => $trips,
            'totalTrips' => Trip::count(),
            'filters' => $this->dataTableService->getSortFilters(Trip::filters()),
            'title' => $this->pageTitle('trip.title_index'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Trip/Create', [
            'destinations' => Destination::all(),
            'typeOptions' => ItemType::options(),
            'categoryOptions' => ItemCategory::options(),
            'transportOptions' => Transport::options(),
            'practicalSections' => PracticalInfo::labels(),
            'title' => $this->pageTitle('trip.title_create'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateTripRequest $request): RedirectResponse
    {
        $trip = new Trip;

        $validatedFiles = $request->safe()->only(['heroImage', 'images']);
        $validatedFields = $request->safe()->except(['heroImage', 'images']);
        $destinations = $request->safe()->destinations ?? [];

        $trip->fill($validatedFields);
        $trip->save();
        $trip->syncImages($validatedFiles['heroImage'], ImageRelation::HeroImage, true);
        $trip->syncImages($validatedFiles['images'], ImageRelation::Images);

        if (count($destinations)) {
            $trip->destinations()->sync($destinations);
        }

        // Sync trip items
        $this->tripItemService::syncTripItems($trip, $request->input('items'));

        return redirect()->route('admin.trips.show', $trip)->with('success', __('trip.created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Trip $trip): Response
    {
        return Inertia::render('Admin/Trip/Show', [
            'trip' => $trip->load(['heroImage', 'images', 'destinations', 'itineraries', 'items']),
            'tripItems' => $this->tripItemService::aggregate($trip),
            'practicalSections' => PracticalInfo::labels(),
            'title' => $this->pageTitle('trip.title_show'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Trip $trip): Response
    {
        return Inertia::render('Admin/Trip/Edit', [
            'trip' => $trip->load(['heroImage', 'images', 'destinations', 'items']),
            'destinations' => Destination::all(),
            'typeOptions' => ItemType::options(),
            'categoryOptions' => ItemCategory::options(),
            'transportOptions' => Transport::options(),
            'practicalSections' => PracticalInfo::labels(),
            'title' => $this->pageTitle('trip.title_edit'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTripRequest $request, Trip $trip): RedirectResponse
    {
        $validatedFiles = $request->safe()->only(['heroImage', 'images']);
        $validatedFields = $request->safe()->except(['heroImage', 'images', 'destinations']);
        $destinations = $request->safe()->destinations ?? [];

        $trip->fill($validatedFields);
        $trip->save();

        // Sync heroImage (handles both existing paths and new uploads)
        if (isset($validatedFiles['heroImage'])) {
            $trip->syncImages($validatedFiles['heroImage'], ImageRelation::HeroImage, true);
        }

        // Sync images array (handles mix of existing paths and new uploads)
        if (isset($validatedFiles['images']) && is_array($validatedFiles['images'])) {
            $trip->syncImages($validatedFiles['images'], ImageRelation::Images);
        }

        if (count($destinations)) {
            $trip->destinations()->sync($destinations);
        }

        // Sync trip items - delete all and recreate
        $trip->items()->delete();
        $this->tripItemService::syncTripItems($trip, $request->input('items'));

        return redirect()->route('admin.trips.show', $trip)
            ->with('success', __('trip.updated'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Trip $trip): RedirectResponse
    {
        Trip::destroy($trip->id);

        return redirect()->route('admin.trips.index')
            ->with('success', __('trip.deleted'));
    }
}
