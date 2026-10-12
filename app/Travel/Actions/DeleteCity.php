<?php

namespace App\Travel\Actions;

use App\Travel\Models\City;
use Illuminate\Validation\ValidationException;

class DeleteCity
{
    /**
     * Delete a city that nothing refers to anymore. The database refuses it otherwise, but without a message the
     * admin can act on.
     *
     * @throws ValidationException
     */
    public function handle(City $city): void
    {
        if ($city->stations()->exists() || $city->accommodations()->exists() || $city->pharmacies()->exists()) {
            throw ValidationException::withMessages([
                'city' => 'Deze plaats heeft nog stations, accommodaties of apotheken. Verwijder die eerst.',
            ]);
        }

        $city->delete();
    }
}
