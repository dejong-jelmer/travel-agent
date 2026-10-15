<?php

namespace App\Travel\Actions;

use App\Travel\Models\Pharmacy;

class UpdatePharmacy
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function handle(Pharmacy $pharmacy, array $attributes): Pharmacy
    {
        $pharmacy->update($attributes);

        return $pharmacy;
    }
}
