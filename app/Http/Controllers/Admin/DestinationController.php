<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Destination\TravelInfo;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasPageMetadata;
use App\Http\Requests\DataTableRequest;
use App\Models\Country;
use App\Models\Destination;
use App\Services\DataTableService;
use App\Services\DestinationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DestinationController extends Controller
{
    use HasPageMetadata;

    public function __construct(private DataTableService $dataTableService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(DataTableRequest $request): Response
    {
        $destinations = $this->dataTableService
            ->applyFilters(Destination::query(), $request, Destination::filters())
            ->paginate()
            ->withQueryString();

        return Inertia::render('Admin/Destination/Index', [
            'destinations' => $destinations,
            'totalDestination' => Destination::count(),
            'filters' => $this->dataTableService->getSortFilters(Destination::filters()),
            'title' => $this->pageTitle('destination.title_index'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Destination/Create', [
            'title' => $this->pageTitle('destination.title_create'),
            'travelInfoSections' => TravelInfo::labels(),
            'countries' => DestinationService::countries(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'country_code' => 'required|string|size:2',
            'region' => 'nullable|string|max:255',
            'travel_info' => 'nullable|array',
            'travel_info.*' => 'nullable|string',
        ]);

        $country = Country::find($validated['country_code']);
        $name = $country->getTranslatedName();

        Destination::create([
            'country_code' => $validated['country_code'],
            'name' => $name,
            'region' => $validated['region'],
            'travel_info' => $validated['travel_info'],
            ]);

        return redirect()->route('admin.destinations.index')->with('success', __('destination.created'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Destination $destination): Response
    {
        return Inertia::render('Admin/Destination/Edit', [
            'title' => $this->pageTitle('destination.title_edit'),
            'destination' => $destination,
            'travelInfoSections' => TravelInfo::labels(),
            'countries' => DestinationService::countries(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Destination $destination): RedirectResponse
    {
        $validated = $request->validate([
            'country_code' => 'required|string|size:2',
            'region' => 'nullable|string|max:10',
            'name' => 'required|string|max:255',
            'travel_info' => 'nullable|array',
            'travel_info.*' => 'nullable|string',
        ]);

        $destination->update($validated);

        return redirect()->route('admin.destinations.index')->with('success', __('destination.updated'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Destination $destination): RedirectResponse
    {
        $trips = $destination->trips->count();
        $feedback = [
            'status' => 'error',
            'message' => trans_choice('destination.delete_failed', $trips, ['trips' => $trips]),
        ];

        if (! $trips) {
            Destination::destroy($destination->id);
            $feedback['status'] = 'success';
            $feedback['message'] = __('destination.deleted');
        }

        return redirect()->route('admin.destinations.index')->with($feedback['status'], $feedback['message']);
    }
}
