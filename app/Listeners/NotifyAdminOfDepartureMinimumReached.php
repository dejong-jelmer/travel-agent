<?php

namespace App\Listeners;

use App\Events\DepartureMinimumReached;
use App\Mail\AdminDepartureMinimumReachedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotifyAdminOfDepartureMinimumReached implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(DepartureMinimumReached $event): void
    {
        $address = config('booking.mail');

        try {
            Mail::to($address)->send(
                new AdminDepartureMinimumReachedMail($event->departure)
            );
        } catch (\Throwable $e) {
            Log::error('Departure minimum reached mail failed', [
                'departure_id' => $event->departure->id,
                'admin_email' => $address,
                'exception' => $e,
            ]);
        }
    }

    public function failed(DepartureMinimumReached $event, \Throwable $exception): void
    {
        Log::critical('Departure minimum reached mail permanently failed after retries', [
            'departure_id' => $event->departure->id,
            'admin_email' => config('booking.mail'),
            'error' => $exception->getMessage(),
        ]);
    }
}
