<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasPageMetadata;
use App\Http\Resources\DepartureResource;
use App\Models\Booking;
use App\Models\Departure;
use App\Models\TripRequest;
use App\Services\SystemHealthService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    use HasPageMetadata;

    public function showDashboard(Request $request, SystemHealthService $healthService)
    {
        $departures = Departure::upcoming()->withParticipantCounts()->withRequestCounts()->with('trip')->orderBy('decision_date')->get();
        $attention = $departures->filter(fn (Departure $departure) => $departure->needsAttention())->values();

        return Inertia::render('Admin/Dashboard', [
            'title' => $this->pageTitle('dashboard.title'),
            'bookings' => [
                'all' => Booking::count(),
                'new' => Booking::new()->count(),
                'upcoming' => Booking::upcoming()->count(),
                'upcomingMonth' => Booking::upcomingMonth()->count(),
            ],
            'tripRequests' => [
                'new' => TripRequest::new()->count(),
                'all' => TripRequest::count(),
            ],
            'departures' => [
                'upcoming' => $departures->count(),
                'attention' => DepartureResource::collection($attention)->resolve($request),
            ],
            'systemHealth' => $healthService->getAllChecks(),
        ]);
    }
}
