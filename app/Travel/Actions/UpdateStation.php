<?php

namespace App\Travel\Actions;

use App\Travel\Models\Station;

class UpdateStation
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function handle(Station $station, array $attributes): Station
    {
        $station->update($attributes);

        return $station;
    }
}
