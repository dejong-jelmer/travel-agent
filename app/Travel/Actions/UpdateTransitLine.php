<?php

namespace App\Travel\Actions;

use App\Travel\Models\TransitLine;
use Illuminate\Validation\ValidationException;

class UpdateTransitLine
{
    /**
     * Update the line. A line keeps its mode while legs use it: those legs only carry lines of their own mode, so a
     * new mode would break them (Invariant::LinesMatchTheLeg), and attaching was the only place that checked it.
     *
     * @param  array<string, mixed>  $attributes
     *
     * @throws ValidationException
     */
    public function handle(TransitLine $line, array $attributes): TransitLine
    {
        $line->fill($attributes);

        if ($line->isDirty('mode') && ($legs = $line->legs()->count()) > 0) {
            throw ValidationException::withMessages([
                'mode' => 'Deze lijn komt voor in '.($legs === 1 ? '1 etappe' : "{$legs} etappes")
                    .' van dit vervoermiddel, dus het vervoermiddel kan niet veranderen.',
            ]);
        }

        $line->save();

        return $line;
    }
}
