<?php

namespace App\Travel\Actions;

use App\Travel\Models\Accommodation;
use App\Travel\Models\Stay;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DeleteAccommodation
{
    /**
     * Delete an accommodation no stay uses and, through its deleted event, its arrival route. The database refuses
     * one with stays, but without a message the admin can act on.
     *
     * @throws ValidationException
     */
    public function handle(Accommodation $accommodation): void
    {
        $stays = Stay::where('accommodation_id', $accommodation->id)->count();

        if ($stays > 0) {
            throw ValidationException::withMessages([
                'accommodation' => 'Deze accommodatie hoort bij '.($stays === 1 ? '1 verblijf' : "{$stays} verblijven")
                    .'. Haal haar daar eerst weg.',
            ]);
        }

        DB::transaction(fn () => $accommodation->delete());
    }
}
