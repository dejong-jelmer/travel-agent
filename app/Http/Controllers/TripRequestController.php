<?php

namespace App\Http\Controllers;

use App\Events\TripRequestCreated;
use App\Http\Controllers\Traits\HasPageMetadata;
use App\Http\Requests\CreateTripRequestRequest;
use App\Models\Trip;
use App\Services\TripRequestService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class TripRequestController extends Controller
{
    use HasPageMetadata;

    public function __construct(private readonly TripRequestService $tripRequestService) {}

    public function store(CreateTripRequestRequest $request, Trip $trip): RedirectResponse
    {
        $tripRequest = $this->tripRequestService->store($request->validated(), $trip);
        $tripRequest->load('trip');

        TripRequestCreated::dispatch($tripRequest);

        return redirect()->route('trip-requests.thanks', $trip);
    }

    public function thanks(Trip $trip): Response
    {
        return Inertia::render('TripRequest/Thanks', [
            'title' => $this->pageTitle(__('trip_request.thanks_page_title')),
            'trip' => $trip,
        ]);
    }
}
