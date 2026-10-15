<?php

namespace App\Travel\Actions;

use App\Travel\Models\Stay;

class UpdateStay
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function handle(Stay $stay, array $attributes): Stay
    {
        $stay->update($attributes);

        return $stay;
    }
}
