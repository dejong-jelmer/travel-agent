<?php

namespace App\Listeners;

use App\Events\TripRequestCreated;
use App\Mail\TripRequestConfirmationMail;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendTripRequestConfirmationEmail
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
        try {
            Mail::to(new Address($event->tripRequest->email, $event->tripRequest->name))->queue(
                new TripRequestConfirmationMail($event->tripRequest)
            );
        } catch (\Throwable $e) {
            Log::error('Trip request confirmation mail failed: '.$e->getMessage(), [
                'trip_request_id' => $event->tripRequest->id,
                'email' => $event->tripRequest->email,
            ]);
            Log::error('Stack trace: '.$e->getTraceAsString());
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(TripRequestCreated $event, \Throwable $exception): void
    {
        Log::critical('Trip request confirmation email permanently failed after retries', [
            'trip_request_id' => $event->tripRequest->id,
            'email' => $event->tripRequest->email,
            'error' => $exception->getMessage(),
        ]);
    }
}
