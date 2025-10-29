<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\SystemHealthService;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function showDashboard()
    {
        $healthService = new SystemHealthService();

        return Inertia::render('Admin/Dashboard', [
            'title' => 'Admin dashboard - '.env('APP_NAME'),
            'bookings' => [
                'new' => Booking::new()->count(),
                'future' => Booking::future()->count(),
                'departDueNextMonth' => Booking::departDueNextMonth()->count(),
            ],
            'systemHealth' => $healthService->getAllChecks(),
        ]);
    }
}
