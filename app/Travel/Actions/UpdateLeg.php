<?php

namespace App\Travel\Actions;

use App\Travel\Models\Leg;
use Illuminate\Support\Facades\DB;

class UpdateLeg
{
    public function __construct(private readonly SyncLegTransitLines $syncTransitLines) {}

    /**
     * Update the leg and replace its transit lines. The lines are always passed, because a new type can rule out
     * the lines the leg had: a tram leg turned into a walk must lose its lines.
     *
     * @param  array<string, mixed>  $attributes
     * @param  list<array{int, string|null}>  $transitLines  [transit_line_id, direction] pairs, in the order shown.
     */
    public function handle(Leg $leg, array $attributes, array $transitLines): Leg
    {
        return DB::transaction(function () use ($leg, $attributes, $transitLines) {
            $leg->update($attributes);
            $this->syncTransitLines->handle($leg, $transitLines);

            return $leg;
        });
    }
}
