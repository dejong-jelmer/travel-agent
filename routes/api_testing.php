<?php

use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

if (! App::environment(['local', 'testing'])) {
    return;
}

Route::middleware(['auth:sanctum'])->prefix('testing')->group(function () {
    Route::post('/boekingen', [BookingController::class, 'store'])
        ->middleware('throttle:frontend-form-actions')
        ->name('api.bookings.store');
    Route::put('/boekingen/{booking}', [AdminBookingController::class, 'update'])
        ->middleware('throttle:frontend-form-actions')
        ->name('api.bookings.update');
});
