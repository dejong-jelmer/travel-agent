<?php

namespace App\Travel\Http\Controllers;

use App\Travel\Actions\CreateLeg;
use App\Travel\Actions\Reorder;
use App\Travel\Http\Requests\LegRequest;
use App\Travel\Http\Requests\ReorderRequest;
use App\Travel\Models\Journey;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

/**
 * The legs of a journey. Updating and deleting a leg goes through LegController, whatever owns it.
 */
class JourneyLegController
{
    public function store(LegRequest $request, Journey $journey, CreateLeg $createLeg): RedirectResponse
    {
        $createLeg->handle($journey, $request->legAttributes(), $request->transitLines());

        return back()->with('success', 'Etappe toegevoegd.');
    }

    public function updateOrder(ReorderRequest $request, Journey $journey, Reorder $reorder): JsonResponse
    {
        $reorder->handle($journey->legs(), $request->ids());

        return response()->json(['success' => true]);
    }
}
