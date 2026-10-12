<?php

namespace App\Travel\Actions;

use App\Travel\Models\Journey;

class UpdateJourney
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function handle(Journey $journey, array $attributes): Journey
    {
        $journey->update($attributes);

        return $journey;
    }
}
