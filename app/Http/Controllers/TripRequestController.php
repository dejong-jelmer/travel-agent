<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\HasPageMetadata;
use App\Http\Requests\CreateTripRequestRequest;
use App\Mail\TripRequestConfirmationMail;
use App\Mail\TripRequestNotificationMail;
use App\Models\Trip;
use App\Services\TripRequestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;

class TripRequestController extends Controller
{
    use HasPageMetadata;

    public function __construct(private readonly TripRequestService $tripRequestService) {}

    public function store(CreateTripRequestRequest $request, Trip $trip): RedirectResponse
    {
        $tripRequest = $this->tripRequestService->store($request->validated(), $trip);
        $tripRequest->load('trip');

        try {
            Mail::to(new Address($tripRequest->email, $tripRequest->name))->queue(
                new TripRequestConfirmationMail($tripRequest)
            );
        } catch (\Throwable $e) {
            Log::error('Trip request confirmation mail failed: '.$e->getMessage(), [
                'trip_request_id' => $tripRequest->id,
                'email' => $tripRequest->email,
            ]);
        }

        try {
            Mail::to(new Address(
                config('contact.mail'),
                config('contact.full_name', config('app.name'))
            ))->queue(
                new TripRequestNotificationMail($tripRequest)
            );
        } catch (\Throwable $e) {
            Log::error('Trip request notification mail failed: '.$e->getMessage(), [
                'trip_request_id' => $tripRequest->id,
                'admin_email' => config('contact.mail'),
            ]);
        }

        return redirect()->route('trip-requests.thanks', $trip);
    }

    public function thanks(Trip $trip): Response
    {
        return Inertia::render('TripRequest/Thanks', [
            'title' => $this->pageTitle(__('trip_request.thanks_page_title')),
            'trip' => $trip,
        ]);
    }
}
