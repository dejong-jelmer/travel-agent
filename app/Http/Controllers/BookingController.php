<?php

namespace App\Http\Controllers;

use App\DTO\CreateBookingData;
use App\Enums\ModelAction;
use App\Events\BookingCreated;
use App\Events\BookingFailed;
use App\Http\Controllers\Traits\HasPageMetadata;
use App\Http\Requests\CreateBookingRequest;
use App\Models\Booking;
use App\Services\BookingService;
use App\Services\PriceCalculatorService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class BookingController extends Controller
{
    use HasPageMetadata;

    public function __construct(private BookingService $bookingService, private PriceCalculatorService $priceCalculator) {}

    public function store(CreateBookingRequest $request): RedirectResponse|JsonResponse
    {
        $bookingData = CreateBookingData::fromRequest($request);
        $totalTravelers = $this->bookingService->getTotalTravellers($bookingData->travelers);

        $prices = $this->attempt(
            fn () => $this->priceCalculator->forTrip($bookingData->trip, $totalTravelers, $bookingData->date),
            'No prices available',
            'booking.error.no_prices_available',
            $bookingData
        );
        if ($prices instanceof RedirectResponse) {
            return $prices;
        }

        $booking = $this->attempt(
            fn () => $this->bookingService->create($bookingData, $prices),
            'Booking create failed',
            'booking.error.create_failed',
            $bookingData
        );
        if ($booking instanceof RedirectResponse) {
            return $booking;
        }

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

    private function attempt(callable $action, string $errorContext, string $errorKey, CreateBookingData $data): mixed
    {
        try {
            return $action();
        } catch (Exception $e) {
            Log::error($e->getMessage());
            event(new BookingFailed($e->getMessage(), $errorContext, [
                'email' => $data->contact->email,
                'trip_name' => $data->trip->name,
                'date' => $data->date->format('d-m-Y'),
            ]));

            return back()->withErrors(['message' => __($errorKey)]);
        }
    }
}
