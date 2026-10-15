<?php

namespace App\Travel\Actions;

use App\Travel\Models\Accommodation;

class UpdateAccommodation
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function handle(Accommodation $accommodation, array $attributes): Accommodation
    {
        $accommodation->update($attributes);

        return $accommodation;
    }
}
