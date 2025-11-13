<?php

namespace App\Http\Controllers;

use App\DTO\StoreBookingData;
use App\Enums\BookingAction;
use App\Events\BookingCreated;
use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

class BookingController extends Controller
{
    public function store(StoreBookingRequest $request, BookingService $bookingService): RedirectResponse|JsonResponse
    {
        $bookingData = StoreBookingData::fromRequest($request);
        $booking = $bookingService->store($bookingData);
        event(new BookingCreated($booking));
        session()->flash('new_booking', $booking->uuid);

        // Response macro in App\Responses\BookingResponse
        return response()->booking($booking, BookingAction::Stored);
    }

    public function confirmation(Booking $booking)
    {
        if (session('new_booking') !== $booking->uuid) {
            abort(404);
        }

        return Inertia::render('Trips/Confirmation', [
            'title' => "Boekingsbevestiging - {$booking->product->name}",
            'booking' => $booking->load([
                'product.countries',
                'product.featuredImage',
                'travelers',
                'contact',
                'mainBooker',
            ]),
        ]);
    }
}
