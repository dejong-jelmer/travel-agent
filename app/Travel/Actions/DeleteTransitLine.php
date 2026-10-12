<?php

namespace App\Travel\Actions;

use App\Travel\Models\TransitLine;
use Illuminate\Validation\ValidationException;

class DeleteTransitLine
{
    /**
     * Delete a line that no leg uses. The database would silently take it off those legs instead.
     *
     * @throws ValidationException
     */
    public function handle(TransitLine $line): void
    {
        $legs = $line->legs()->count();

        if ($legs > 0) {
            throw ValidationException::withMessages([
                'transit_line' => 'Deze lijn komt voor in '.($legs === 1 ? '1 etappe' : "{$legs} etappes").'. Haal hem daar eerst weg.',
            ]);
        }

        $line->delete();
    }
}
