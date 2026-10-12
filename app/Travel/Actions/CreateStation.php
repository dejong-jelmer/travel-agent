<?php

namespace App\Travel\Actions;

use App\Travel\Models\Station;

class CreateStation
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function handle(array $attributes): Station
    {
        return Station::create($attributes);
    }
}
