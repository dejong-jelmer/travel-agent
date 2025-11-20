<?php

namespace App\Http\Controllers;

use App\DTO\CreateBookingData;
use App\Enums\ModelAction;
use App\Events\BookingCreated;
use App\Http\Requests\CreateBookingRequest;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

class BookingController extends Controller
{
    public function store(CreateBookingRequest $request, BookingService $bookingService): RedirectResponse|JsonResponse
    {
        $bookingData = CreateBookingData::fromRequest($request);
        $booking = $bookingService->create($bookingData);
        event(new BookingCreated($booking));
        session()->flash('new_booking', $booking->uuid);

        // Response macro in App\Responses\BookingResponse
        return response()->booking($booking, ModelAction::Created);
    }

    public function confirmation(Booking $booking)
    {
        if (session('new_booking') !== $booking->uuid) {
            abort(404);
        }

        return Inertia::render('Booking/Received', [
            'title' => "Boeking ontvangen - {$booking->product->name}",
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
