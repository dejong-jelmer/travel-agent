<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Booking\Status as BookingStatus;
use App\Enums\Trip\TripFormat;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasPageMetadata;
use App\Http\Requests\DepartureRequest;
use App\Http\Resources\DepartureBookingResource;
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
     * The departures of all trips: what needs the admin's attention first, then what is coming up, then the most
     * recent departures that are over or cancelled.
     */
    public function overview(Request $request): Response
    {
        $upcoming = Departure::upcoming()->withParticipantCounts()->with('trip')->orderBy('departs_on')->get();
        [$attention, $rest] = $upcoming->partition(fn (Departure $departure) => $departure->needsAttention());

        $past = Departure::withParticipantCounts()->with('trip')
            ->where(fn ($query) => $query->whereNotNull('cancelled_at')->orWhereDate('returns_on', '<', today()))
            ->orderByDesc('departs_on')
            ->limit(20)
            ->get();

        return Inertia::render('Admin/Departures/Index', [
            // The most urgent first: the decision that is due the soonest
            'attention' => DepartureResource::collection($attention->sortBy('decision_date')->values())->resolve($request),
            'upcoming' => DepartureResource::collection($rest->values())->resolve($request),
            'past' => DepartureResource::collection($past)->resolve($request),
            'title' => $this->pageTitle('departure.title_index'),
        ]);
    }

    /**
     * A departure with its participant list: every booking on it, the ones that hold places first.
     */
    public function show(Request $request, Departure $departure): Response
    {
        $departure->load('trip');

        $bookings = $departure->bookings()
            ->with(['travelers', 'contact'])
            ->orderByRaw('CASE WHEN status = ? THEN 1 ELSE 0 END', [BookingStatus::Canceled->value])
            ->orderBy('id')
            ->get();

        return Inertia::render('Admin/Departures/Show', [
            'departure' => (new DepartureResource($departure))->resolve($request),
            'bookings' => DepartureBookingResource::collection($bookings)->resolve($request),
            'title' => $this->pageTitle('departure.title_show'),
        ]);
    }

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
