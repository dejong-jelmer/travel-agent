<?php

use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Route;

Route::post('/boekingen', [BookingController::class, 'store'])->name('api.bookings.store');
