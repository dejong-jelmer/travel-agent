<?php

namespace App\Travel\Actions;

use App\Travel\Models\Leg;
use App\Travel\Models\Station;
use Illuminate\Validation\ValidationException;

class DeleteStation
{
    /**
     * Delete a station that no leg uses. The database would clear the station of those legs instead, and leave a
     * transfer without its station.
     *
     * @throws ValidationException
     */
    public function handle(Station $station): void
    {
        $legs = Leg::where('from_station_id', $station->id)->orWhere('to_station_id', $station->id)->count();

        if ($legs > 0) {
            throw ValidationException::withMessages([
                'station' => 'Dit station komt voor in '.($legs === 1 ? '1 etappe' : "{$legs} etappes").'. Haal het daar eerst weg.',
            ]);
        }

        $station->delete();
    }
}
