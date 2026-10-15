<?php

namespace App\Travel\Actions;

use App\Travel\Models\TransitLine;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DeleteTransitLine
{
    /**
     * Delete a line that no leg uses. The database would silently take it off those legs instead.
     *
     * The line is locked first, so a leg that takes it at the same time waits for the delete and fails on its foreign
     * key, rather than the delete failing on it without a message.
     *
     * @throws ValidationException
     */
    public function handle(TransitLine $line): void
    {
        DB::transaction(function () use ($line) {
            TransitLine::whereKey($line->id)->lockForUpdate()->first();

            $legs = $line->legs()->count();

            if ($legs > 0) {
                throw ValidationException::withMessages([
                    'transit_line' => 'Deze lijn komt voor in '.($legs === 1 ? '1 etappe' : "{$legs} etappes").'. Haal hem daar eerst weg.',
                ]);
            }

            $line->delete();
        });
    }
}
