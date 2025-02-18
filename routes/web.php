<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use Inertia\Inertia;

Route::get('/user/{user}', [UserController::class, 'show']);

Route::get('/', function() {
    return Inertia::render('Home');
});

Route::get('/about', function() {
    return Inertia::render('About');
});

Route::get('/contact', function() {
    return Inertia::render('Contact');
});
