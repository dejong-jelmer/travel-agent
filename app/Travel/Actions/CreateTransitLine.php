<?php

namespace App\Travel\Actions;

use App\Travel\Models\TransitLine;

class CreateTransitLine
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function handle(array $attributes): TransitLine
    {
        return TransitLine::create($attributes);
    }
}
