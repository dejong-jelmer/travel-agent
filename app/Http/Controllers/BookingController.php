<?php

namespace App\Http\Controllers;

use App\DTO\CreateBookingData;
use App\Enums\ModelAction;
use App\Events\BookingCreated;
use App\Http\Controllers\Traits\HasPageMetadata;
use App\Http\Requests\CreateBookingRequest;
use App\Models\Booking;
use App\Services\BookingService;
use App\Services\PriceCalculatorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

class BookingController extends Controller
{
    use HasPageMetadata;

    public function __construct(private BookingService $bookingService, private PriceCalculatorService $priceCalculator) {}

    public function store(CreateBookingRequest $request): RedirectResponse|JsonResponse
    {
        $bookingData = CreateBookingData::fromRequest($request);
        $totalTravelers = $this->bookingService->getTotalTravellers($bookingData->travelers);

        $prices = $this->priceCalculator->forTrip($bookingData->trip, $totalTravelers, $bookingData->date);

        $booking = $this->bookingService->create($bookingData, $prices);
        event(new BookingCreated($booking));
        session()->flash('booking_uuid', $booking->uuid);

        // Response macro in App\Responses\BookingResponse
        return response()->booking($booking, ModelAction::Created);
    }

    public function received(Booking $booking)
    {
        if (session('booking_uuid') !== $booking->uuid) {
            abort(404);
        }

        return Inertia::render('Booking/Received', [
            'title' => $this->pageTitle('booking.title_received'),
            'booking' => $booking->load([
                'trip.destinations',
                'trip.heroImage',
                'travelers',
                'contact',
                'mainBooker',
            ]),
        ]);
    }
}
