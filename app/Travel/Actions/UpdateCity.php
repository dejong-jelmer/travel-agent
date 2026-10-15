<?php

namespace App\Travel\Actions;

use App\Travel\Models\City;

class UpdateCity
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function handle(City $city, array $attributes): City
    {
        $city->update($attributes);

        return $city;
    }
}
