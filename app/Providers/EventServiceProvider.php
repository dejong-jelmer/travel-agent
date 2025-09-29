<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Events\BookingCreated;
use App\Listeners\SendBookingConfirmationMail;
use App\Listeners\NotifyAdminOfNewBooking;

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
