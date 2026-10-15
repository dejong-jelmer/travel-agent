<?php

namespace App\Travel\Http\Controllers;

use App\Travel\Actions\CreatePharmacy;
use App\Travel\Actions\DeletePharmacy;
use App\Travel\Actions\UpdatePharmacy;
use App\Travel\Http\Requests\PharmacyRequest;
use App\Travel\Models\City;
use App\Travel\Models\Pharmacy;
use Illuminate\Http\RedirectResponse;

/**
 * The pharmacies of a city. Updating and deleting one goes by the pharmacy alone.
 */
class PharmacyController
{
    public function store(PharmacyRequest $request, City $city, CreatePharmacy $createPharmacy): RedirectResponse
    {
        $createPharmacy->handle($city, $request->validated());

        return back()->with('success', 'Apotheek toegevoegd.');
    }

    public function update(PharmacyRequest $request, Pharmacy $pharmacy, UpdatePharmacy $updatePharmacy): RedirectResponse
    {
        $updatePharmacy->handle($pharmacy, $request->validated());

        return back()->with('success', 'Apotheek bijgewerkt.');
    }

    public function destroy(Pharmacy $pharmacy, DeletePharmacy $deletePharmacy): RedirectResponse
    {
        $deletePharmacy->handle($pharmacy);

        return back()->with('success', 'Apotheek verwijderd.');
    }
}
