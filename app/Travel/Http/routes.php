<?php

use App\Travel\Http\Controllers\AccommodationLegController;
use App\Travel\Http\Controllers\JourneyController;
use App\Travel\Http\Controllers\JourneyLegController;
use App\Travel\Http\Controllers\LegController;
use App\Travel\Http\Controllers\StayController;
use Illuminate\Support\Facades\Route;

// The admin routes of the travel module, with the prefix, names and middleware of the admin routes in routes/web.php.
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['web', 'auth', 'admin'])
    ->group(function () {
        Route::resource('bookings.journeys', JourneyController::class)->shallow()->only(['store', 'update', 'destroy']);
        Route::patch('/bookings/{booking}/journeys/order', [JourneyController::class, 'updateOrder'])->name('bookings.journeys.order');

        Route::resource('bookings.stays', StayController::class)->shallow()->only(['store', 'update', 'destroy']);
        Route::patch('/bookings/{booking}/stays/order', [StayController::class, 'updateOrder'])->name('bookings.stays.order');

        Route::resource('journeys.legs', JourneyLegController::class)->only(['store']);
        Route::patch('/journeys/{journey}/legs/order', [JourneyLegController::class, 'updateOrder'])->name('journeys.legs.order');

        Route::resource('accommodations.legs', AccommodationLegController::class)->only(['store']);
        Route::patch('/accommodations/{accommodation}/legs/order', [AccommodationLegController::class, 'updateOrder'])->name('accommodations.legs.order');

        Route::resource('legs', LegController::class)->only(['update', 'destroy']);
    });
