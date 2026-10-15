<?php

namespace App\Travel\Actions;

use App\Travel\Models\City;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DeleteCity
{
    /**
     * Delete a city that nothing refers to anymore. The database refuses it otherwise, but without a message the
     * admin can act on.
     *
     * The city is locked first, so a station, accommodation or pharmacy added to it at the same time waits for the
     * delete and fails on its foreign key, rather than the delete failing on it without a message.
     *
     * @throws ValidationException
     */
    public function handle(City $city): void
    {
        DB::transaction(function () use ($city) {
            City::whereKey($city->id)->lockForUpdate()->first();

            if ($city->stations()->exists() || $city->accommodations()->exists() || $city->pharmacies()->exists()) {
                throw ValidationException::withMessages([
                    'city' => 'Deze plaats heeft nog stations, accommodaties of apotheken. Verwijder die eerst.',
                ]);
            }

            $city->delete();
        });
    }
}
