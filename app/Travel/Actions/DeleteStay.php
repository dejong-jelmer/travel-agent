<?php

namespace App\Travel\Actions;

use App\Travel\Models\Stay;

class DeleteStay
{
    /**
     * Delete the stay. The other stays keep their positions: the gap is harmless.
     */
    public function handle(Stay $stay): void
    {
        $stay->delete();
    }
}
