<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ProductController;
use Inertia\Inertia;

Route::get('/', function() {
    return Inertia::render('Homepage/Home');
});

Route::get('/about', function() {
    return Inertia::render('Homepage/About', [
        'title' => 'Over mij - TussenTijd Reizen'
    ]);
});

Route::get('/contact', function() {
    return Inertia::render('Homepage/Contact', [
        'title' => 'Contact - TussenTijd Reizen'
    ]);
});

Route::get('/admin', function() {
    return Inertia::render('Auth/Login', [
        'title' => 'Admin - TussenTijd Reizen'
    ]);
})->name('admin');
Route::post('admin/login', [AuthController::class, 'login'])->middleware('guest')->name('admin.login');
Route::group(['prefix' => 'admin', 'middleware'=> 'auth'], function () {
    Route::get('/logout', [AuthController::class, 'logout'])->name('admin.logout');
    Route::get('/dashboard', function() {
        return Inertia::render('Admin/Dashboard', [
            'title' => 'Admin dashboard - TussenTijd Reizen'
        ]);
    })->name('admin.dashboard');

    Route::resource('/products', ProductController::class);
});
