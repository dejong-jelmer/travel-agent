<?php

namespace App\Http\Controllers\Admin;

use App\DTO\DataTableConfigData;
use App\DTO\UpdateBookingData;
use App\Enums\Booking\PaymentStatus;
use App\Enums\Booking\Status;
use App\Enums\ModelAction;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasDataTableFilters;
use App\Http\Controllers\Traits\HasPageMetadata;
use App\Http\Requests\UpdateBookingRequest;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class BookingController extends Controller
{
    use HasDataTableFilters,
        HasPageMetadata;

    public function __construct(private BookingService $bookingService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $query = Booking::with(['trip']);

        // Apply DataTable filters
        $this->applyDataTableFilters($query, new DataTableConfigData(
            searchable: ['reference'],
            searchableRelations: ['trip.name'],
            filterable: ['status', 'payment_status'],
            sortable: ['id', 'reference', 'status', 'payment_status', 'departure_date', 'trip'],
            belongsToSorts: [
                'trip' => [
                    'table' => 'trips',
                    'foreign_key' => 'trip_id',
                    'column' => 'name',
                ],
            ],
            defaultSort: ['id', 'desc']
        ));

        return Inertia::render('Admin/Booking/Index', [
            'bookings' => $query->paginate()->withQueryString(),
            'totalBookings' => Booking::count(),
            'filters' => $this->getCurrentFilters(['status', 'payment_status']),
            'statusOptions' => Status::options(),
            'paymentStatusOptions' => PaymentStatus::options(),
            'title' => $this->pageTitle('booking.title_index'),
        ]);
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
}
