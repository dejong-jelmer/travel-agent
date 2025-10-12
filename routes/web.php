<?php

use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ItineraryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsletterController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Homepage routes
Route::get('/', [HomeController::class, 'home'])->name('home');
Route::get('/over-ons', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact']);
Route::post('/contact', [HomeController::class, 'submitContact'])->name('contact');
Route::get('/privacybeleid', [HomeController::class, 'showPrivacy'])->name('privacy');
Route::get('/algemene-voorwaarden', [HomeController::class, 'showTerms'])->name('terms');

// Newsletter
Route::post('/nieuwsbrief/aanmelden', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
Route::get('/nieuwsbrief/bevestigen/{token}', [NewsletterController::class, 'confirm'])->name('newsletter.confirm');
Route::get('/nieuwsbrief/afmelden/{token}', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');

// Trips
Route::get('reizen/{trip:slug}', [HomeController::class, 'showTrip'])->name('trip.show');

// Booking routes
Route::post('/boekingen', [BookingController::class, 'store'])->name('bookings.store');
Route::get('/boekingen/{booking:uuid}/bevestiging', [BookingController::class, 'confirmation'])->middleware('nocache')->name('bookings.confirmation');

// Admin routes
Route::get('/admin/login', function () {
    return Inertia::render('Auth/Login', [
        'title' => 'Admin - '.env('APP_NAME'),
    ]);
})->middleware('guest')->name('admin');

Route::get('admin/', fn () => to_route('admin.login'));
Route::post('admin/login', [AuthController::class, 'login'])->middleware('guest')->name('admin.login');

Route::prefix('admin')
    ->name('admin.')
    ->middleware('auth')
    ->group(function () {
        Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [DashboardController::class, 'showDashboard'])->name('dashboard');

        // Product routes
        Route::resource('/products', ProductController::class)->except(['update']);
        Route::post('/products/update/{product}', [ProductController::class, 'update'])->name('products.update');

        // Product Itinerary routes
        Route::resource('products.itineraries', ItineraryController::class)->except(['show', 'edit', 'update', 'destroy']);
        Route::patch('/products/{product}/itineraries/order', [ItineraryController::class, 'updateOrder'])->name('products.itineraries.order');

        // Itinerary routes
        Route::resource('itineraries', ItineraryController::class)->only(['edit', 'destroy']);
        Route::post('/itineraries/{itinerary}', [ItineraryController::class, 'update'])->name('itineraries.update');

        // Country routes
        Route::resource('countries', CountryController::class)->except(['show', 'edit', 'update']);

        // Booking routes
        Route::resource('bookings', AdminBookingController::class)->except(['create', 'store']);
    });
