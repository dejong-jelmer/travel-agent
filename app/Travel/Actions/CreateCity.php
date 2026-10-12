<?php

namespace App\Travel\Actions;

use App\Travel\Models\City;

class CreateCity
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function handle(array $attributes): City
    {
        return City::create($attributes);
    }
}
