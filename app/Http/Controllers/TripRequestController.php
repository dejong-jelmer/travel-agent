<?php

namespace App\Http\Controllers;

use App\Events\TripRequestCreated;
use App\Http\Requests\CreateTripRequestRequest;
use App\Models\Trip;
use App\Services\TripRequestService;
use Illuminate\Http\RedirectResponse;

class TripRequestController extends Controller
{
    public function __construct(private readonly TripRequestService $tripRequestService) {}

    public function store(CreateTripRequestRequest $request, Trip $trip): RedirectResponse
    {
        $tripRequest = $this->tripRequestService->store($request->validated(), $trip);
        $tripRequest->load('trip');

        TripRequestCreated::dispatch($tripRequest);

        // Back to the trip page, where the form shows a toast
        return redirect()->route('trips.show', $trip);
    }
}
