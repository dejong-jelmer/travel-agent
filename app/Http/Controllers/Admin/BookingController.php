<?php

namespace App\Http\Controllers\Admin;

use App\DTO\UpdateBookingData;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateBookingRequest;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class BookingController extends Controller
{
    public function __construct(private BookingService $bookingService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('Admin/Bookings/Index', [
            'bookings' => Booking::with(['product', 'adults', 'children', 'mainBooker'])->paginate(10),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking): Response
    {
        $this->bookingService->seen($booking);

        return Inertia::render('Admin/Bookings/Show', [
            'booking' => $booking->load(['product', 'contact', 'adults', 'children', 'mainBooker']),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Booking $booking): Response
    {
        $this->bookingService->seen($booking);

        return Inertia::render('Admin/Bookings/Edit', [
            'db_booking' => $booking->load(['product', 'contact', 'travelers', 'adults', 'mainBooker']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookingRequest $request, Booking $booking): RedirectResponse
    {
        $bookingData = UpdateBookingData::fromRequest($request);
        $booking = $this->bookingService->update($booking, $bookingData);

        return redirect()->route('admin.bookings.index')->with('success', "De aan passing op boeking:{$booking->reference} is geslaagd");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booking $booking)
    {
        // $this->bookingService->updateChangeLog($booking->id, $booking, ['deleted_at' => now()]);
        Booking::destroy($booking->id);

        return redirect()->route('admin.bookings.index')
            ->with('success', __('De boeking is  verwijderd'));
    }
}
