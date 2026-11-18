<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TripRequest\Status;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasPageMetadata;
use App\Http\Requests\DataTableRequest;
use App\Http\Requests\UpdateTripRequestRequest;
use App\Http\Resources\DepartureResource;
use App\Models\Booking;
use App\Models\Departure;
use App\Models\TripRequest;
use App\Services\DataTableService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class TripRequestController extends Controller
{
    use HasPageMetadata;

    public function __construct(private DataTableService $dataTableService) {}

    public function index(DataTableRequest $request): Response
    {
        $tripRequests = $this->dataTableService
            ->applyFilters(TripRequest::with(['trip', 'departure']), $request, TripRequest::filters())
            ->paginate()
            ->withQueryString();

        return Inertia::render('Admin/Request/Index', [
            'tripRequests' => $tripRequests,
            'totalTripRequests' => TripRequest::count(),
            'filters' => $this->dataTableService->getSortFilters(TripRequest::filters()),
            'statusOptions' => Status::options(),
            'title' => $this->pageTitle('trip_request.title_index'),
        ]);
    }

    public function edit(TripRequest $tripRequest): Response
    {
        $bookingOptions = Booking::with(['trip' => fn ($q) => $q->withTrashed()])
            ->whereNull('anonymized_at')
            ->orderByDesc('id')
            ->get()
            ->map(fn (Booking $booking) => [
                'id' => $booking->id,
                'name' => $booking->reference.' - '.$booking->trip->name,
            ])
            ->all();

        $departure = $tripRequest->departure_id
            ? Departure::withParticipantCounts()->find($tripRequest->departure_id)
            : null;

        return Inertia::render('Admin/Request/Edit', [
            'tripRequest' => $tripRequest->load('trip'),
            'travelersLabel' => $tripRequest->travelersLabel(),
            // The departure of a request for a group trip, with how many places it has left now
            'departure' => $departure ? (new DepartureResource($departure))->resolve(request()) : null,
            'statusOptions' => Status::options(),
            'bookingOptions' => $bookingOptions,
            'title' => $this->pageTitle('trip_request.title_edit'),
        ]);
    }

    public function update(UpdateTripRequestRequest $request, TripRequest $tripRequest): RedirectResponse
    {
        $tripRequest->update($request->validated());

        return redirect()->route('admin.trip-requests.edit', $tripRequest)
            ->with('success', __('trip_request.updated'));
    }

    public function destroy(TripRequest $tripRequest): RedirectResponse
    {
        $name = $tripRequest->name;
        $tripRequest->delete();

        return redirect()->route('admin.trip-requests.index')
            ->with('success', __('trip_request.deleted', ['name' => $name]));
    }
}
