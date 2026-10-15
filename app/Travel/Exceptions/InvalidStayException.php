<?php

namespace App\Travel\Exceptions;

use App\Travel\Models\Stay;
use App\Travel\Validation\Invariant;
use DomainException;

/**
 * A stay was about to be written in a state that breaks its invariants. A safety net, like InvalidLegException.
 */
class InvalidStayException extends DomainException
{
    /**
     * @param  list<Invariant>  $broken
     */
    public static function for(Stay $stay, array $broken): self
    {
        $names = implode(', ', array_map(fn (Invariant $invariant) => $invariant->name, $broken));

        return new self('Stay '.($stay->getKey() ?? '(new)')." breaks: {$names}.");
    }
}
