<?php

namespace App\Travel\Actions;

use App\Travel\Models\Leg;
use App\Travel\Models\Station;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DeleteStation
{
    /**
     * Delete a station that no leg uses. The database would clear the station of those legs instead, and leave a
     * transfer without its station.
     *
     * The station is locked first: a leg that takes it at the same time has to wait for the delete, as its foreign key
     * checks the station, and then fails on that key instead of losing its station unseen.
     *
     * @throws ValidationException
     */
    public function handle(Station $station): void
    {
        DB::transaction(function () use ($station) {
            Station::whereKey($station->id)->lockForUpdate()->first();

            $legs = Leg::where('from_station_id', $station->id)->orWhere('to_station_id', $station->id)->count();

            if ($legs > 0) {
                throw ValidationException::withMessages([
                    'station' => 'Dit station komt voor in '.($legs === 1 ? '1 etappe' : "{$legs} etappes").'. Haal het daar eerst weg.',
                ]);
            }

            $station->delete();
        });
    }
}
