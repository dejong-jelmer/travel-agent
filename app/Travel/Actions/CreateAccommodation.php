<?php

namespace App\Travel\Actions;

use App\Travel\Models\Accommodation;

class CreateAccommodation
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function handle(array $attributes): Accommodation
    {
        return Accommodation::create($attributes);
    }
}
