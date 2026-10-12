<?php

namespace App\Travel\Actions;

use App\Travel\Models\City;
use App\Travel\Models\Pharmacy;

class CreatePharmacy
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function handle(City $city, array $attributes): Pharmacy
    {
        return $city->pharmacies()->create($attributes);
    }
}
