<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Trip\TripFormat;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasPageMetadata;
use App\Http\Requests\DepartureRequest;
use App\Http\Resources\DepartureResource;
use App\Models\Departure;
use App\Models\Trip;
use App\Services\DepartureService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class DepartureController extends Controller
{
    use HasPageMetadata;

    public function __construct(private DepartureService $departures) {}

    /**
     * The departures of a group trip, the first to depart first.
     */
    public function index(Request $request, Trip $trip): Response
    {
        $departures = $trip->departures()->withParticipantCounts()->get();

        return Inertia::render('Admin/Trip/Departures/Index', [
            'trip' => $trip->only(['id', 'name', 'slug', 'duration']),
            'isGroupTrip' => $trip->format === TripFormat::Group,
            'departures' => DepartureResource::collection($departures)->resolve($request),
            'title' => $this->pageTitle('departure.title_index'),
        ]);
    }

    public function store(DepartureRequest $request, Trip $trip): RedirectResponse
    {
        $this->departures->create($trip, $request->validated());

        return back()->with('success', __('departure.created'));
    }

    public function update(DepartureRequest $request, Departure $departure): RedirectResponse
    {
        $this->departures->update($departure, $request->validated());

        return back()->with('success', __('departure.updated'));
    }

    public function destroy(Departure $departure): RedirectResponse
    {
        try {
            $this->departures->delete($departure);
        } catch (ValidationException $e) {
            return back()->with('error', $e->validator->errors()->first());
        }

        return back()->with('success', __('departure.deleted'));
    }

    /**
     * Promise that the departure goes ahead. Only the admin does this; nothing guarantees a departure by itself.
     */
    public function guarantee(Departure $departure): RedirectResponse
    {
        if ($departure->isCancelled() || $departure->isCompleted()) {
            return back()->with('error', __('departure.validation.not_guaranteeable'));
        }

        $departure->guarantee();

        return back()->with('success', __('departure.guaranteed'));
    }

    public function cancel(Request $request, Departure $departure): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:3', 'max:1000'],
        ]);

        $canceledBookings = $this->departures->cancel($departure, $validated['reason']);

        return back()->with('success', trans_choice('departure.cancelled', $canceledBookings, ['count' => $canceledBookings]));
    }
}
