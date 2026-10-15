<?php

namespace App\Travel\Http\Controllers;

use App\Travel\Actions\CreateCity;
use App\Travel\Actions\DeleteCity;
use App\Travel\Actions\UpdateCity;
use App\Travel\Http\Requests\CityRequest;
use App\Travel\Models\City;
use Illuminate\Http\RedirectResponse;

class CityController
{
    public function store(CityRequest $request, CreateCity $createCity): RedirectResponse
    {
        $createCity->handle($request->validated());

        return back()->with('success', 'Plaats toegevoegd.');
    }

    public function update(CityRequest $request, City $city, UpdateCity $updateCity): RedirectResponse
    {
        $updateCity->handle($city, $request->validated());

        return back()->with('success', 'Plaats bijgewerkt.');
    }

    public function destroy(City $city, DeleteCity $deleteCity): RedirectResponse
    {
        $deleteCity->handle($city);

        return back()->with('success', 'Plaats verwijderd.');
    }
}
