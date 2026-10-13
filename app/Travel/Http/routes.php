<?php

use App\Travel\Http\Controllers\AccommodationController;
use App\Travel\Http\Controllers\AccommodationLegController;
use App\Travel\Http\Controllers\CityController;
use App\Travel\Http\Controllers\JourneyController;
use App\Travel\Http\Controllers\JourneyLegController;
use App\Travel\Http\Controllers\LegController;
use App\Travel\Http\Controllers\LibraryController;
use App\Travel\Http\Controllers\PharmacyController;
use App\Travel\Http\Controllers\SharedDocumentController;
use App\Travel\Http\Controllers\ShareLinkController;
use App\Travel\Http\Controllers\StationController;
use App\Travel\Http\Controllers\StayController;
use App\Travel\Http\Controllers\TransitLineController;
use App\Travel\Http\Controllers\TravelDocumentController;
use App\Travel\Http\Controllers\TravelPlanController;
use App\Travel\Http\PageProps;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Route;

// The admin routes of the travel module, with the prefix, names and middleware of the admin routes in routes/web.php.
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['web', 'auth', 'admin'])
    ->group(function () {
        Route::get('/bookings/{booking}/travel-plan', [TravelPlanController::class, 'show'])->name('bookings.travel-plan');
        Route::get('/bookings/{booking}/travel-documents', [TravelDocumentController::class, 'show'])->name('bookings.travel-documents');
        Route::post('/bookings/{booking}/share-link', [ShareLinkController::class, 'store'])->name('bookings.share-link.store');
        Route::delete('/bookings/{booking}/share-link', [ShareLinkController::class, 'destroy'])->name('bookings.share-link.destroy');

        Route::resource('bookings.journeys', JourneyController::class)->shallow()->only(['store', 'update', 'destroy']);
        Route::patch('/bookings/{booking}/journeys/order', [JourneyController::class, 'updateOrder'])->name('bookings.journeys.order');

        Route::resource('bookings.stays', StayController::class)->shallow()->only(['store', 'update', 'destroy']);
        Route::patch('/bookings/{booking}/stays/order', [StayController::class, 'updateOrder'])->name('bookings.stays.order');

        Route::resource('journeys.legs', JourneyLegController::class)->only(['store']);
        Route::patch('/journeys/{journey}/legs/order', [JourneyLegController::class, 'updateOrder'])->name('journeys.legs.order');

        Route::resource('accommodations.legs', AccommodationLegController::class)->only(['store']);
        Route::patch('/accommodations/{accommodation}/legs/order', [AccommodationLegController::class, 'updateOrder'])->name('accommodations.legs.order');

        Route::resource('legs', LegController::class)->only(['update', 'destroy']);

        // The library the plans pick from.
        Route::get('/travel-library/{section?}', [LibraryController::class, 'show'])
            ->whereIn('section', PageProps::LIBRARY_SECTIONS)
            ->name('travel-library');

        Route::resource('cities', CityController::class)->only(['store', 'update', 'destroy']);
        Route::resource('cities.pharmacies', PharmacyController::class)->shallow()->only(['store', 'update', 'destroy']);
        Route::resource('stations', StationController::class)->only(['store', 'update', 'destroy']);
        Route::resource('transit-lines', TransitLineController::class)->only(['store', 'update', 'destroy']);
        Route::resource('accommodations', AccommodationController::class)->only(['store', 'update', 'destroy']);
    });

// The documents behind a share link, for the travelers. Without the web group: the page needs no session, so it sets
// no cookies. The token is the only key, so the routes are throttled.
Route::prefix('reisdocumenten')
    ->name('travel.documents.')
    ->middleware([SubstituteBindings::class, 'throttle:60,1'])
    ->group(function () {
        Route::get('/lettertypen/{font}', [SharedDocumentController::class, 'font'])
            ->whereIn('font', ['manrope-latin.woff2', 'manrope-latin-ext.woff2'])
            ->name('font');
        Route::get('/{shareLink}', [SharedDocumentController::class, 'show'])->name('shared');
        Route::get('/{shareLink}/verblijf', [SharedDocumentController::class, 'stays'])->name('shared.stays');
        Route::get('/{shareLink}/praktisch', [SharedDocumentController::class, 'practical'])->name('shared.practical');
        Route::get('/{shareLink}/pdf', [SharedDocumentController::class, 'pdf'])->name('shared.pdf');
    });
