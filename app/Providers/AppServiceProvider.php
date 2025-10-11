<?php

namespace App\Providers;

use App\Helpers\Breadcrumbs;
use App\Responses\BookingResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Illuminate\Support\Facades\Response;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment('testing')) {
            $this->app->register(FakerPhoneServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Inertia::share([
            'flash' => function () {
                return [
                    'success' => Session::get('success'),
                    'error' => Session::get('error'),
                ];
            },
        ]);

        Inertia::share('breadcrumbs', fn() => Breadcrumbs::generate());

        Response::macro('booking', function ($booking) {
            return BookingResponse::make($booking)->toResponse();
        });
    }
}
