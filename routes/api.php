<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use Illuminate\Support\Facades\Route;

Route::post('/boekingen', [BookingController::class, 'store'])->name('api.bookings.store');
Route::put('/boekingen/{booking}', [AdminBookingController::class, 'update'])->name('api.bookings.update');
