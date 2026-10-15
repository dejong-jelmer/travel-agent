<?php

namespace App\Travel\Validation;

use App\Travel\Models\Stay;

class StayInvariants
{
    /**
     * The invariants a stay breaks. Works on an unsaved stay too.
     *
     * @return list<Invariant>
     */
    public static function check(Stay $stay): array
    {
        if ($stay->arrival !== null && $stay->departure !== null && $stay->departure->lt($stay->arrival)) {
            return [Invariant::DatesRunForward];
        }

        return [];
    }
}
