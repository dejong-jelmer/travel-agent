<?php

namespace App\Providers;

use App\Events\BookingCreated;
use App\Events\BookingFailed;
use App\Events\NewsletterSubscriptionRequested;
use App\Events\TripRequestCreated;
use App\Listeners\NotifyAdminOfFailedBooking;
use App\Listeners\NotifyAdminOfNewBooking;
use App\Listeners\NotifyAdminOfNewsletterSubscription;
use App\Listeners\NotifyAdminOfTripRequest;
use App\Listeners\SendBookingConfirmationEmail;
use App\Listeners\SendNewsletterConfirmationEmail;
use App\Listeners\SendTripRequestConfirmationEmail;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        BookingCreated::class => [
            SendBookingConfirmationEmail::class,
            NotifyAdminOfNewBooking::class,
        ],
        BookingFailed::class => [
            NotifyAdminOfFailedBooking::class,
        ],
        NewsletterSubscriptionRequested::class => [
            SendNewsletterConfirmationEmail::class,
            NotifyAdminOfNewsletterSubscription::class,
        ],
        TripRequestCreated::class => [
            SendTripRequestConfirmationEmail::class,
            NotifyAdminOfTripRequest::class,
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
