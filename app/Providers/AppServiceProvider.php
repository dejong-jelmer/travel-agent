<?php

namespace App\Providers;

use App\Enums\BookingAction;
use App\Helpers\Breadcrumbs;
use App\Models\Booking;
use App\Responses\BookingResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

/**
 * @method static \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse booking(\App\Models\Booking $booking, \App\Enums\BookingAction $action)
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
        ]);

        Inertia::share('breadcrumbs', fn () => Breadcrumbs::generate());

        Response::macro('booking', function (Booking $booking, BookingAction $action) {
            return BookingResponse::make($booking)->toResponse($action);
        });
    }
}
