<?php

namespace App\Travel\Http\Controllers;

use App\Travel\Actions\CreateAccommodation;
use App\Travel\Actions\DeleteAccommodation;
use App\Travel\Actions\UpdateAccommodation;
use App\Travel\Http\Requests\AccommodationRequest;
use App\Travel\Models\Accommodation;
use Illuminate\Http\RedirectResponse;

class AccommodationController
{
    public function store(AccommodationRequest $request, CreateAccommodation $createAccommodation): RedirectResponse
    {
        $createAccommodation->handle($request->validated());

        return back()->with('success', 'Accommodatie toegevoegd.');
    }

    public function update(AccommodationRequest $request, Accommodation $accommodation, UpdateAccommodation $updateAccommodation): RedirectResponse
    {
        $updateAccommodation->handle($accommodation, $request->validated());

        return back()->with('success', 'Accommodatie bijgewerkt.');
    }

    public function destroy(Accommodation $accommodation, DeleteAccommodation $deleteAccommodation): RedirectResponse
    {
        $deleteAccommodation->handle($accommodation);

        return back()->with('success', 'Accommodatie verwijderd.');
    }
}
