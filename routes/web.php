<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use Inertia\Inertia;

Route::get('/', function() {
    return Inertia::render('Home');
});

Route::get('/about', function() {
    return Inertia::render('About', [
        'title' => 'Over mij - TussenTijd Reizen'
    ]);
});

Route::get('/contact', function() {
    return Inertia::render('Contact', [
        'title' => 'Contact - TussenTijd Reizen'
    ]);
});
