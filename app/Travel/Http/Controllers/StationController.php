<?php

namespace App\Travel\Http\Controllers;

use App\Travel\Actions\CreateStation;
use App\Travel\Actions\DeleteStation;
use App\Travel\Actions\UpdateStation;
use App\Travel\Http\Requests\StationRequest;
use App\Travel\Models\Station;
use Illuminate\Http\RedirectResponse;

class StationController
{
    public function store(StationRequest $request, CreateStation $createStation): RedirectResponse
    {
        $createStation->handle($request->validated());

        return back()->with('success', 'Station toegevoegd.');
    }

    public function update(StationRequest $request, Station $station, UpdateStation $updateStation): RedirectResponse
    {
        $updateStation->handle($station, $request->validated());

        return back()->with('success', 'Station bijgewerkt.');
    }

    public function destroy(Station $station, DeleteStation $deleteStation): RedirectResponse
    {
        $deleteStation->handle($station);

        return back()->with('success', 'Station verwijderd.');
    }
}
