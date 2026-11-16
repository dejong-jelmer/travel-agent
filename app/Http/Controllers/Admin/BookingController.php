<?php

namespace App\Http\Controllers\Admin;

use App\DTO\CreateBookingData;
use App\DTO\UpdateBookingData;
use App\Enums\Booking\CostCategory;
use App\Enums\Booking\PaymentStatus;
use App\Enums\Booking\Status;
use App\Enums\ModelAction;
use App\Enums\SettingKey;
use App\Events\BookingCreated;
use App\Events\BookingFailed;
use App\Exceptions\NoPriceAvailableException;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasPageMetadata;
use App\Http\Requests\CreateBookingRequest;
use App\Http\Requests\DataTableRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Http\Resources\DepartureResource;
use App\Models\Booking;
use App\Models\Departure;
use App\Models\Setting;
use App\Models\Trip;
use App\Services\BookingService;
use App\Services\CountryService;
use App\Services\DataTableService;
use App\Services\FeesAndFundsService;
use App\Services\PriceCalculatorService;
use App\Support\MoneyHelper;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class BookingController extends Controller
{
    use HasPageMetadata;

    public function __construct(
        private BookingService $bookingService,
        private DataTableService $dataTableService,
        private FeesAndFundsService $feesAndFunds,
        private PriceCalculatorService $priceCalculator,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(DataTableRequest $request): Response
    {
        $bookings = $this->dataTableService
            ->applyFilters(Booking::with(['trip']), $request, Booking::filters())
            ->paginate()
            ->withQueryString();

        return Inertia::render('Admin/Booking/Index', [
            'bookings' => $bookings,
            'totalBookings' => Booking::count(),
            'filters' => $this->dataTableService->getSortFilters(Booking::filters()),
            'statusOptions' => Status::options(),
            'paymentStatusOptions' => PaymentStatus::options(),
            'title' => $this->pageTitle('booking.title_index'),
        ]);
    }

    public function create(Request $request): Response
    {
        // A group trip is booked on one of its departures that still has to leave
        $trips = Trip::with(['departures' => fn ($departures) => $departures
            ->upcoming()
            ->whereDate('departs_on', '>', today())
            ->withParticipantCounts(),
        ])->get();

        return Inertia::render('Admin/Booking/Create', [
            'trips' => $trips->map(fn (Trip $trip) => [
                ...$trip->toArray(),
                'departures' => $trip->departures
                    ->map(fn (Departure $departure) => $this->bookableDeparture($request, $departure->setRelation('trip', $trip)))
                    ->all(),
            ])->all(),
            'countries' => CountryService::countries(),
            'cost_categories' => CostCategory::options(),
            'default_margin_basis_points' => (int) Setting::get(SettingKey::DefaultBookingMarginBasisPoints, Booking::DEFAULT_MARGIN_BASIS_POINTS),
            'default_margin_amount' => $this->settingInCents(SettingKey::DefaultBookingMarginAmount),
            'default_fee_per_person' => $this->settingInCents(SettingKey::DefaultBookingFeePerPerson),
            'fees_and_funds' => $this->feesAndFunds->asCents(),
            'title' => $this->pageTitle('booking.title_create'),
        ]);
    }

    public function store(CreateBookingRequest $request): RedirectResponse
    {
        $bookingData = CreateBookingData::fromRequest($request);

        try {
            $booking = $this->bookingService->create($bookingData);
        } catch (ValidationException $e) {
            // The departure ran out of places between the form and the save: an answer for the form, not a failure
            throw $e;
        } catch (Exception $e) {
            $this->handleBookingError($e, 'Booking create failed', $bookingData);

            return back()->withErrors(['message' => __('booking.error.create_failed')]);
        }
        event(new BookingCreated($booking));

        return redirect()->route('admin.bookings.index')
            ->with('success', __('booking.created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking): Response
    {
        return Inertia::render('Admin/Booking/Show', [
            'booking' => $booking->load(['trip', 'departure', 'contact', 'adults', 'children', 'mainBooker', 'tripRequest', 'costItems']),
            'cost_categories' => CostCategory::options(),
            'title' => $this->pageTitle('booking.title_show'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Booking $booking): Response
    {
        return Inertia::render('Admin/Booking/Edit', [
            'db_booking' => $booking->load(['trip', 'departure', 'contact', 'travelers', 'adults', 'mainBooker', 'costItems']),
            'statusOptions' => Status::options(),
            'paymentStatusOptions' => PaymentStatus::options(),
            'cost_categories' => CostCategory::options(),
            'default_margin_basis_points' => (int) Setting::get(SettingKey::DefaultBookingMarginBasisPoints, Booking::DEFAULT_MARGIN_BASIS_POINTS),
            // Show what was snapshotted on this booking, not what the settings say today.
            'fees_and_funds' => $booking->fees_and_funds ?? $this->feesAndFunds->asCents(),
            'title' => $this->pageTitle('booking.title_edit'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookingRequest $request, Booking $booking): RedirectResponse|JsonResponse
    {
        $bookingData = UpdateBookingData::fromRequest($request);
        $booking = $this->bookingService->update($booking, $bookingData);

        // Response macro in App\Responses\BookingResponse
        return response()->booking($booking, ModelAction::Updated);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booking $booking)
    {
        Booking::destroy($booking->id);

        return redirect()->route('admin.bookings.index')
            ->with('success', __('booking.deleted', ['reference' => $booking->reference]));
    }

    /**
     * A departure as the booking wizard offers it: with the price a booking on it gets, in cents, whether that
     * comes from the departure or from the trip. Both are null when neither has a price; it cannot be booked then.
     *
     * @return array<string, mixed>
     */
    private function bookableDeparture(Request $request, Departure $departure): array
    {
        try {
            $price = $this->priceCalculator->forDeparture($departure, persons: 1, singleRooms: 1);
        } catch (NoPriceAvailableException) {
            $price = null;
        }

        return [
            ...(new DepartureResource($departure))->resolve($request),
            'booking_price_per_person' => $price ? (int) $price->perPerson->getAmount() : null,
            'booking_single_supplement' => $price ? (int) $price->singleSupplement->getAmount() : null,
        ];
    }

    /**
     * A euro amount from the settings in cents, or null when it is not set.
     */
    private function settingInCents(SettingKey $key): ?int
    {
        $euros = Setting::get($key);

        return $euros === null ? null : MoneyHelper::toCents($euros);
    }

    private function handleBookingError(Exception $e, string $context, CreateBookingData $data): void
    {
        Log::error($e->getMessage());
        event(new BookingFailed($e->getMessage(), $context, [
            'email' => $data->contact->email,
            'trip_name' => $data->trip->name,
            'date' => $data->date->format('d-m-Y'),
        ]));
    }
}
