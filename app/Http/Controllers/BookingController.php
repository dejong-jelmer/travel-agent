<?php

namespace App\Http\Controllers;

use App\DTO\StoreBookingData;
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

        if(request()->is('api/*')) {
            return response()->json([
                'message' => 'Booking created',
                'booking' => [
                    'id' => $booking->id,
                    'travelers' => $booking->travelers->pluck('id')
                ]
            ], 200);
        }

        return to_route('bookings.confirmation', ['booking' => $booking])->with('success', 'Je boeking is geslaagd');
    }

    public function confirmation(Booking $booking)
    {
        if (session('new_booking') !== $booking->uuid) {
            abort(404);
        }

        return Inertia::render('Trips/Confirmation', [
            'title' => "Boekingsbevestiging - {$booking->product->name}",
            'booking' => $booking,
        ]);
    }
}
