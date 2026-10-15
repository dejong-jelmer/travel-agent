<?php

namespace App\Travel\Http\Controllers;

use App\Travel\Actions\CreateLeg;
use App\Travel\Actions\Reorder;
use App\Travel\Http\Requests\LegRequest;
use App\Travel\Http\Requests\ReorderRequest;
use App\Travel\Models\Accommodation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

/**
 * The legs of an accommodation's arrival route. Updating and deleting a leg goes through LegController.
 */
class AccommodationLegController
{
    public function store(LegRequest $request, Accommodation $accommodation, CreateLeg $createLeg): RedirectResponse
    {
        $createLeg->handle($accommodation, $request->legAttributes(), $request->transitLines());

        return back()->with('success', 'Etappe toegevoegd.');
    }

    public function updateOrder(ReorderRequest $request, Accommodation $accommodation, Reorder $reorder): JsonResponse
    {
        $reorder->handle($accommodation->legs(), $request->ids());

        return response()->json(['success' => true]);
    }
}
