<?php

namespace App\Travel\Exceptions;

use App\Travel\Models\Leg;
use App\Travel\Validation\Invariant;
use DomainException;

/**
 * A leg was about to be written in a state that breaks its invariants. A safety net for seeders, tinker and
 * factories: the admin sees the form request's messages instead.
 */
class InvalidLegException extends DomainException
{
    /**
     * @param  list<Invariant>  $broken
     */
    public static function for(Leg $leg, array $broken): self
    {
        $names = implode(', ', array_map(fn (Invariant $invariant) => $invariant->name, $broken));

        return new self('Leg '.($leg->getKey() ?? '(new)')." breaks: {$names}.");
    }
}
