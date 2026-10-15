<?php

namespace App\Travel\Http\Controllers;

use App\Models\Booking;
use App\Travel\Actions\CreateJourney;
use App\Travel\Actions\DeleteJourney;
use App\Travel\Actions\Reorder;
use App\Travel\Actions\UpdateJourney;
use App\Travel\Http\Requests\JourneyRequest;
use App\Travel\Http\Requests\ReorderRequest;
use App\Travel\Models\Journey;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class JourneyController
{
    public function store(JourneyRequest $request, Booking $booking, CreateJourney $createJourney): RedirectResponse
    {
        $createJourney->handle($booking, $request->validated());

        return back()->with('success', 'Reis toegevoegd.');
    }

    public function update(JourneyRequest $request, Journey $journey, UpdateJourney $updateJourney): RedirectResponse
    {
        $updateJourney->handle($journey, $request->validated());

        return back()->with('success', 'Reis bijgewerkt.');
    }

    public function destroy(Journey $journey, DeleteJourney $deleteJourney): RedirectResponse
    {
        $deleteJourney->handle($journey);

        return back()->with('success', 'Reis verwijderd.');
    }

    public function updateOrder(ReorderRequest $request, Booking $booking, Reorder $reorder): JsonResponse
    {
        $reorder->handle($booking->journeys(), $request->ids());

        return response()->json(['success' => true]);
    }
}
