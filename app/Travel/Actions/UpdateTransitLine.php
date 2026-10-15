<?php

namespace App\Travel\Actions;

use App\Travel\Models\TransitLine;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UpdateTransitLine
{
    /**
     * Update the line. A line keeps its mode while legs use it: those legs only carry lines of their own mode, so a
     * new mode would break them (Invariant::LinesMatchTheLeg), and attaching was the only place that checked it.
     *
     * The line is locked first. A leg that takes it at the same time reads its mode with a shared lock in
     * SyncLegTransitLines, so it either waits for the new mode, or finishes first and is counted here.
     *
     * @param  array<string, mixed>  $attributes
     *
     * @throws ValidationException
     */
    public function handle(TransitLine $line, array $attributes): TransitLine
    {
        return DB::transaction(function () use ($line, $attributes) {
            TransitLine::whereKey($line->id)->lockForUpdate()->first();

            $line->fill($attributes);

            if ($line->isDirty('mode') && ($legs = $line->legs()->count()) > 0) {
                throw ValidationException::withMessages([
                    'mode' => 'Deze lijn komt voor in '.($legs === 1 ? '1 etappe' : "{$legs} etappes")
                        .' van dit vervoermiddel, dus het vervoermiddel kan niet veranderen.',
                ]);
            }

            $line->save();

            return $line;
        });
    }
}
