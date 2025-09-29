<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Http\Requests\BookingRequest;
use Illuminate\Http\RedirectResponse;
use App\DTO\BookingData;
use Inertia\Inertia;
use App\Events\BookingCreated;
use App\Services\BookingService;

class BookingController extends Controller
{
    public function store(BookingRequest $request, BookingService $bookingService): RedirectResponse
    {
        $bookingData = BookingData::fromRequest($request);
        $booking = $bookingService->store($bookingData);
        event(new BookingCreated($booking));
        session()->flash('new_booking', $booking->uuid);
        return to_route('bookings.confirmation', ['booking' => $booking])->with('success', 'Je boeking is geslaagd');
    }

    public function confirmation(Booking $booking)
    {
        if (session('new_booking') !== $booking->uuid) {
            abort(403);
        }

        return Inertia::render('Trips/Confirmation', [
            'title' => "Boekingsbevestiging - {$booking->product->name}",
            'booking' => $booking
        ]);
    }
}
