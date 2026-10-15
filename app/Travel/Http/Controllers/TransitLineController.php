<?php

namespace App\Travel\Http\Controllers;

use App\Travel\Actions\CreateTransitLine;
use App\Travel\Actions\DeleteTransitLine;
use App\Travel\Actions\UpdateTransitLine;
use App\Travel\Http\Requests\TransitLineRequest;
use App\Travel\Models\TransitLine;
use Illuminate\Http\RedirectResponse;

class TransitLineController
{
    public function store(TransitLineRequest $request, CreateTransitLine $createTransitLine): RedirectResponse
    {
        $createTransitLine->handle($request->validated());

        return back()->with('success', 'Lijn toegevoegd.');
    }

    public function update(TransitLineRequest $request, TransitLine $transitLine, UpdateTransitLine $updateTransitLine): RedirectResponse
    {
        $updateTransitLine->handle($transitLine, $request->validated());

        return back()->with('success', 'Lijn bijgewerkt.');
    }

    public function destroy(TransitLine $transitLine, DeleteTransitLine $deleteTransitLine): RedirectResponse
    {
        $deleteTransitLine->handle($transitLine);

        return back()->with('success', 'Lijn verwijderd.');
    }
}
