<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasPageTitle;
use App\Models\Booking;
use App\Services\SystemHealthService;
use Inertia\Inertia;

class DashboardController extends Controller
{
    use HasPageTitle;

    public function showDashboard(SystemHealthService $healthService)
    {
        return Inertia::render('Admin/Dashboard', [
            'title' => $this->pageTitle('Dashboard'),
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
