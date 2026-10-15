<?php

namespace App\Travel\Actions;

use App\Travel\Models\Pharmacy;

class DeletePharmacy
{
    public function handle(Pharmacy $pharmacy): void
    {
        $pharmacy->delete();
    }
}
