<?php

namespace App\Providers;

use App\Enums\ModelAction;
use App\Helpers\Breadcrumbs;
use App\Models\Booking;
use App\Models\Setting;
use App\Models\Trip;
use App\Responses\BookingResponse;
use App\Services\CountryService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

/**
 * @method static \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse booking(\App\Models\Booking $booking, \App\Enums\ModelAction $action)
 * */
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
            'adminStats' => fn () => request()->routeIs('admin.*') && Auth::check()
                ? ['newBookingsCount' => Booking::new()->count()]
                : null,
            'settings' => fn () => Setting::pluck('value', 'key')->all(),
            'navCountries' => fn () => Cache::remember('nav_countries', 3600, fn () => $this->app->make(CountryService::class)->getCountriesForTrips(
                Trip::with('destinations.country')->published()->get()
            )
            ),
        ]);

        Inertia::share('breadcrumbs', fn () => Breadcrumbs::generate());

        Response::macro('booking', function (Booking $booking, ModelAction $action) {
            return BookingResponse::make($booking)->toResponse($action);
        });
    }
}
