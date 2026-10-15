<?php

namespace App\Travel\Http\Controllers;

use App\Travel\Actions\DeleteLeg;
use App\Travel\Actions\UpdateLeg;
use App\Travel\Http\Requests\LegRequest;
use App\Travel\Models\Leg;
use Illuminate\Http\RedirectResponse;

class LegController
{
    public function update(LegRequest $request, Leg $leg, UpdateLeg $updateLeg): RedirectResponse
    {
        $updateLeg->handle($leg, $request->legAttributes(), $request->transitLines());

        return back()->with('success', 'Etappe bijgewerkt.');
    }

    public function destroy(Leg $leg, DeleteLeg $deleteLeg): RedirectResponse
    {
        $deleteLeg->handle($leg);

        return back()->with('success', 'Etappe verwijderd.');
    }
}
