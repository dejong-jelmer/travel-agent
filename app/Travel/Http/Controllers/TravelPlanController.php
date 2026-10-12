<?php

namespace App\Travel\Http\Controllers;

use App\Models\Booking;
use App\Travel\Http\PageProps;
use Inertia\Inertia;
use Inertia\Response;

class TravelPlanController
{
    public function show(Booking $booking): Response
    {
        return Inertia::render('Admin/Travel/Plan', [
            'title' => "Reisplan {$booking->reference} | ".config('app.name'),
            ...PageProps::forPlan($booking),
        ]);
    }
}
