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
     * The accommodation is locked first, so a stay that takes it at the same time waits for the delete and fails on
     * its foreign key, rather than the delete failing on it without a message.
     *
     * @throws ValidationException
     */
    public function handle(Accommodation $accommodation): void
    {
        DB::transaction(function () use ($accommodation) {
            Accommodation::whereKey($accommodation->id)->lockForUpdate()->first();

            $stays = Stay::where('accommodation_id', $accommodation->id)->count();

            if ($stays > 0) {
                throw ValidationException::withMessages([
                    'accommodation' => 'Deze accommodatie hoort bij '.($stays === 1 ? '1 verblijf' : "{$stays} verblijven")
                        .'. Haal haar daar eerst weg.',
                ]);
            }

            $accommodation->delete();
        });
    }
}
