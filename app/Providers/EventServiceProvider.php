<?php

namespace App\Providers;

use App\Events\BookingCreated;
use App\Listeners\NotifyAdminOfNewBooking;
use App\Listeners\SendBookingConfirmationMail;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        BookingCreated::class => [
            SendBookingConfirmationMail::class,
            NotifyAdminOfNewBooking::class,
        ],
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
