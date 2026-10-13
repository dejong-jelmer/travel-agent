<?php

namespace App\Travel\Actions;

use App\Travel\Exceptions\InvalidLegException;
use App\Travel\Models\Leg;
use App\Travel\Models\TransitLine;
use App\Travel\Validation\LegInvariants;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class SyncLegTransitLines
{
    /**
     * Give the leg exactly these transit lines, positioned in the given order. Each line has its own direction, as
     * on the Bordeaux tram leg where line C and line D head for different termini.
     *
     * @param  list<array{int, string|null}>  $lines  [transit_line_id, direction] pairs, in the order shown.
     *
     * @throws ModelNotFoundException When a line does not exist: findMany() would leave it out of the check unseen.
     * @throws InvalidLegException When the lines do not fit the leg type.
     */
    public function handle(Leg $leg, array $lines): void
    {
        $broken = LegInvariants::checkTransitLines($leg->type, TransitLine::findOrFail(array_column($lines, 0)));

        if ($broken !== []) {
            throw InvalidLegException::for($leg, $broken);
        }

        $pivot = [];
        foreach ($lines as $position => [$transitLineId, $direction]) {
            $pivot[$transitLineId] = ['direction' => $direction, 'position' => $position];
        }

        DB::transaction(fn () => $leg->transitLines()->sync($pivot));
    }
}
