<?php

namespace App\Providers;

use App\DTO\ContactDetails;
use App\Services\AntiSpamEmailService;
use App\Services\ContactDetailsService;
use App\Services\PhoneNumberService;
use Illuminate\Support\ServiceProvider;

class ContactDetailsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(ContactDetailsService::class, function ($app) {
            $details = new ContactDetails(
                address: config('contact.address'),
                postal: config('contact.postal'),
                city: config('contact.city'),
                mapsLink: config('contact.maps'),
                kvk: config('contact.kvk'),
                btw: config('contact.btw'),
                mail: new AntiSpamEmailService(config('contact.mail')),
                phone: new PhoneNumberService(config('contact.phone'))
            );

            return new ContactDetailsService($details);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
