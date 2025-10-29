<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\SystemHealthService;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function showDashboard(SystemHealthService $healthService)
    {
        return Inertia::render('Admin/Dashboard', [
            'title' => 'Admin dashboard - '.config('app.name'),
            'bookings' => [
                'all' => Booking::count(),
                'new' => Booking::new()->count(),
                'upcoming' => Booking::upcoming()->count(),
                'upcomingMonth' => Booking::upcomingMonth()->count(),
            ],
            'systemHealth' => $healthService->getAllChecks(),
        ]);
    }
}
