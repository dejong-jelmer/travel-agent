<?php

namespace App\Travel\Actions;

use App\Travel\Actions\Concerns\AssignsNextPosition;
use App\Travel\Models\Accommodation;
use App\Travel\Models\Journey;
use App\Travel\Models\Leg;
use Illuminate\Support\Facades\DB;

class CreateLeg
{
    use AssignsNextPosition;

    public function __construct(private readonly SyncLegTransitLines $syncTransitLines) {}

    /**
     * Add a leg after the last one of its journey or arrival route, together with its transit lines: if the lines
     * are refused, the leg is not created either.
     *
     * @param  array<string, mixed>  $attributes
     * @param  list<array{int, string|null}>  $transitLines  [transit_line_id, direction] pairs, in the order shown.
     */
    public function handle(Journey|Accommodation $legable, array $attributes, array $transitLines = []): Leg
    {
        return DB::transaction(function () use ($legable, $attributes, $transitLines) {
            $leg = $legable->legs()->create([
                ...$attributes,
                'position' => $this->nextPosition($legable->legs()),
            ]);

            $this->syncTransitLines->handle($leg, $transitLines);

            return $leg;
        });
    }
}
