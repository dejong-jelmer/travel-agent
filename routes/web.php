<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ItineraryController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Homepage routes
Route::get('/', [HomeController::class, 'home'])->name('home');
Route::get('/over-ons', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact']);
Route::post('/contact', [HomeController::class, 'submitContact'])->name('contact');
Route::get('/{trip:slug}', [HomeController::class, 'showTrip'])->name('trip.show');

// Admin routes
Route::get('/admin/login', function () {
    return Inertia::render('Auth/Login', [
        'title' => 'Admin - '.env('APP_NAME'),
    ]);
})->middleware('guest')->name('admin');
Route::post('admin/login', [AuthController::class, 'login'])->middleware('guest')->name('admin.login');

Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function () {
    Route::get('/logout', [AuthController::class, 'logout'])->name('admin.logout');
    Route::get('/dashboard', function () {
        return Inertia::render('Admin/Dashboard', [
            'title' => 'Admin dashboard - '.env('APP_NAME'),
        ]);
    })->name('admin.dashboard');

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

});
