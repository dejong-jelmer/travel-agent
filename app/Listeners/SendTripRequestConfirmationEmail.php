<?php

namespace App\Listeners;

use App\Events\TripRequestCreated;
use App\Mail\TripRequestConfirmationMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendTripRequestConfirmationEmail implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(TripRequestCreated $event): void
    {
        try {
            Mail::to(new Address($event->tripRequest->email, $event->tripRequest->name))->send(
                new TripRequestConfirmationMail($event->tripRequest)
            );
        } catch (\Exception $e) {
            Log::error('Failed to send trip request confirmation email', [
                'trip_request_id' => $event->tripRequest->id,
                'email' => $event->tripRequest->email,
                'exception' => $e,
            ]);
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
