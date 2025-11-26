<?php

use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ItineraryController;
use App\Http\Controllers\Admin\TripController as AdminTripController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\TripController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Homepage routes
Route::get('/', [HomeController::class, 'home'])->name('home');
Route::get('/over-ons', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact']);
Route::get('/privacybeleid', [HomeController::class, 'privacy'])->name('privacy');
Route::get('/algemene-voorwaarden', [HomeController::class, 'terms'])->name('terms');

// contact form
Route::post('/contact', [HomeController::class, 'submitContact'])
    ->middleware('throttle:frontend-form-actions')
    ->name('contact');

// Newsletter
Route::post('/nieuwsbrief/aanmelden', [NewsletterController::class, 'subscribe'])
    ->middleware('throttle:frontend-form-actions')
    ->name('newsletter.subscribe');
Route::get('/nieuwsbrief/bevestigen/{token}', [NewsletterController::class, 'confirm'])->name('newsletter.confirm');
Route::get('/nieuwsbrief/afmelden/{token}', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');

// Trips
Route::get('reizen/{trip:slug}', [TripController::class, 'show'])->name('trip.show');

// Booking routes
Route::post('/boekingen', [BookingController::class, 'store'])
    ->middleware('throttle:frontend-form-actions')
    ->name('bookings.store');
Route::get('/boekingen/{booking:uuid}/bevestiging', [BookingController::class, 'received'])->middleware('nocache')->name('booking.received');

// Admin routes
Route::get('/admin/login', function () {
    return Inertia::render('Auth/Login', [
        'title' => __('auth.title_login').' - '.config('app.name'),
    ]);
})->middleware('guest')->name('admin');

Route::get('admin/', fn () => Auth::check() ? to_route('admin.dashboard') : to_route('admin.login'));
Route::post('admin/login', [AuthController::class, 'login'])->middleware('guest')->name('admin.login');

Route::prefix('admin')
    ->name('admin.')
    ->middleware('auth')
    ->group(function () {
        Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [DashboardController::class, 'showDashboard'])->name('dashboard');

        // Trip routes
        Route::resource('/trips', AdminTripController::class)->except(['update']);
        Route::post('/trips/update/{trip}', [AdminTripController::class, 'update'])->name('trips.update');

        // Product Itinerary routes
        Route::resource('trips.itineraries', ItineraryController::class)->except(['show', 'edit', 'update', 'destroy']);
        Route::patch('/trips/{trip}/itineraries/order', [ItineraryController::class, 'updateOrder'])->name('trips.itineraries.order');

        // Itinerary routes
        Route::resource('itineraries', ItineraryController::class)->only(['edit', 'destroy']);
        Route::post('/itineraries/{itinerary}', [ItineraryController::class, 'update'])->name('itineraries.update');

        // Country routes
        Route::resource('countries', CountryController::class)->except(['show', 'edit', 'update']);

        // Booking routes
        Route::resource('bookings', AdminBookingController::class)->except(['create', 'store']);
    });

// Test production health check
Route::get('/health', function () {
    try {
        // Check database connection
        DB::connection()->getPdo();

        return response()->json([
            'status' => 'healthy',
            'timestamp' => now()->toIso8601String(),
            'environment' => app()->environment(),
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'unhealthy',
            'error' => 'Database connection failed',
        ], 503);
    }
});
