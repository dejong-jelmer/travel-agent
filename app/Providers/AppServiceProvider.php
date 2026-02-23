<?php

namespace App\Providers;

use App\Enums\ModelAction;
use App\Helpers\Breadcrumbs;
use App\Models\Booking;
use App\Models\Setting;
use App\Responses\BookingResponse;
use Illuminate\Support\Facades\Auth;
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
            'bookingSeasonEnd' => fn () => Setting::get('booking_season_end'),
        ]);

        Inertia::share('breadcrumbs', fn () => Breadcrumbs::generate());

        Response::macro('booking', function (Booking $booking, ModelAction $action) {
            return BookingResponse::make($booking)->toResponse($action);
        });
    }
}
