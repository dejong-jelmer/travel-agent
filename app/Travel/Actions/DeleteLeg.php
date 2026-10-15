<?php

namespace App\Travel\Actions;

use App\Travel\Models\Leg;

class DeleteLeg
{
    /**
     * Delete the leg; the database removes its transit line rows. The other legs keep their positions: the gap is
     * harmless.
     */
    public function handle(Leg $leg): void
    {
        $leg->delete();
    }
}
