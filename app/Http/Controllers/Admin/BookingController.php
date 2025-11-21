<?php

namespace App\Http\Controllers\Admin;

use App\DTO\UpdateBookingData;
use App\Enums\Booking\PaymentStatus;
use App\Enums\Booking\Status;
use App\Enums\ModelAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateBookingRequest;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class BookingController extends Controller
{
    private string $appName;

    public function __construct(private BookingService $bookingService)
    {
        $this->appName = config('app.name');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('Admin/Booking/Index', [
            'bookings' => Booking::with(['trip', 'adults', 'children', 'mainBooker'])->paginate(),
            'title' => __('booking.title_index').' - '.$this->appName,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking): Response
    {
        return Inertia::render('Admin/Booking/Show', [
            'booking' => $booking->load(['trip', 'contact', 'adults', 'children', 'mainBooker']),
            'title' => __('booking.title_show').' - '.$this->appName,
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
            'title' => __('booking.title_edit').' - '.$this->appName,
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
}
