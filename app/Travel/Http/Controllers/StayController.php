<?php

namespace App\Travel\Http\Controllers;

use App\Models\Booking;
use App\Travel\Actions\CreateStay;
use App\Travel\Actions\DeleteStay;
use App\Travel\Actions\Reorder;
use App\Travel\Actions\UpdateStay;
use App\Travel\Http\Requests\ReorderRequest;
use App\Travel\Http\Requests\StayRequest;
use App\Travel\Models\Stay;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class StayController
{
    public function store(StayRequest $request, Booking $booking, CreateStay $createStay): RedirectResponse
    {
        $createStay->handle($booking, $request->validated());

        return back()->with('success', 'Verblijf toegevoegd.');
    }

    public function update(StayRequest $request, Stay $stay, UpdateStay $updateStay): RedirectResponse
    {
        $updateStay->handle($stay, $request->validated());

        return back()->with('success', 'Verblijf bijgewerkt.');
    }

    public function destroy(Stay $stay, DeleteStay $deleteStay): RedirectResponse
    {
        $deleteStay->handle($stay);

        return back()->with('success', 'Verblijf verwijderd.');
    }

    public function updateOrder(ReorderRequest $request, Booking $booking, Reorder $reorder): JsonResponse
    {
        $reorder->handle($booking->stays(), $request->ids());

        return response()->json(['success' => true]);
    }
}
