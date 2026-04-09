<?php

namespace App\Http\Controllers\Admin;

use App\DTO\CreateBookingData;
use App\DTO\UpdateBookingData;
use App\Enums\Booking\PaymentStatus;
use App\Enums\Booking\Status;
use App\Enums\ModelAction;
use App\Events\BookingCreated;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasPageMetadata;
use App\Http\Requests\CreateBookingRequest;
use App\Http\Requests\DataTableRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Models\Booking;
use App\Models\Trip;
use App\Services\BookingService;
use App\Services\CountryService;
use App\Services\DataTableService;
use App\Services\PriceCalculatorService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class BookingController extends Controller
{
    use HasPageMetadata;

    public function __construct(private BookingService $bookingService, private DataTableService $dataTableService, private PriceCalculatorService $priceCalculator) {}

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

    public function create(): Response
    {
        return Inertia::render('Admin/Booking/Create', [
            'trip' => Trip::first(),
            'countries' => CountryService::countries(),
            'title' => $this->pageTitle('booking.title_create'),
        ]);
    }

    public function store(CreateBookingRequest $request): RedirectResponse
    {

        $bookingData = CreateBookingData::fromRequest($request);
        $totalTravelers = $this->bookingService->getTotalTravellers($bookingData->travelers);

        try {
            $prices = $this->priceCalculator->forTrip($bookingData->trip, $totalTravelers, $bookingData->date);
        } catch (Exception $e) {
            $this->handleBookingError($e, 'No prices available', $bookingData);

            return back()->withErrors(['message' => __('booking.error.no_prices_available')]);
        }

        try {
            $booking = $this->bookingService->create($bookingData, $prices);
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
            'booking' => $booking->load(['trip', 'contact', 'adults', 'children', 'mainBooker']),
            'title' => $this->pageTitle('booking.title_show'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Booking $booking): Response
    {
        return Inertia::render('Admin/Booking/Edit', [
            'db_booking' => $booking->load(['trip', 'contact', 'travelers', 'adults', 'mainBooker']),
            'statusOptions' => Status::options(),
            'paymentStatusOptions' => PaymentStatus::options(),
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
