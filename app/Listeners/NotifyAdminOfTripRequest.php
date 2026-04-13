<?php

namespace App\Listeners;

use App\Events\TripRequestCreated;
use App\Mail\TripRequestNotificationMail;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotifyAdminOfTripRequest
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TripRequestCreated $event): void
    {
        $address = new Address(
            config('contact.mail'),
            config('contact.full_name', config('app.name'))
        );

        try {
            Mail::to($address)->queue(
                new TripRequestNotificationMail($event->tripRequest)
            );
        } catch (\Throwable $e) {
            Log::error('Trip request notification mail failed: '.$e->getMessage(), [
                'trip_request_id' => $event->tripRequest->id,
                'admin_email' => config('contact.mail'),
            ]);
            Log::error('Stack trace: '.$e->getTraceAsString());
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(TripRequestCreated $event, \Throwable $exception): void
    {
        Log::critical('Trip request admin notification email permanently failed after retries', [
            'trip_request_id' => $event->tripRequest->id,
            'admin_email' => config('contact.mail'),
            'error' => $exception->getMessage(),
        ]);
    }
}
