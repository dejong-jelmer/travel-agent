<?php

namespace App\Http\Controllers;

use App\DTO\CreateBookingData;
use App\Enums\ModelAction;
use App\Events\BookingCreated;
use App\Http\Controllers\Traits\HasPageMetadata;
use App\Http\Requests\CreateBookingRequest;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

class BookingController extends Controller
{
    use HasPageMetadata;

    public function store(CreateBookingRequest $request, BookingService $bookingService): RedirectResponse|JsonResponse
    {
        $bookingData = CreateBookingData::fromRequest($request);
        $booking = $bookingService->create($bookingData);
        event(new BookingCreated($booking));
        session()->flash('booking_uuid', $booking->uuid);

        // Response macro in App\Responses\BookingResponse
        return response()->booking($booking, ModelAction::Created);
    }

    public function received(Booking $booking)
    {
        if (session('booking_uuid') !== $booking->uuid) {
            // abort(404);
        }

        return Inertia::render('Booking/Received', [
            'title' => $this->pageTitle('booking.title_received'),
            'booking' => $booking->load([
                'trip.countries',
                'trip.heroImage',
                'travelers',
                'contact',
                'mainBooker',
            ]),
        ]);
    }
}
