<?php

namespace App\Travel\Actions;

use App\Travel\Models\Journey;
use Illuminate\Support\Facades\DB;

class DeleteJourney
{
    /**
     * Delete the journey and, through its deleted event, its legs. The other journeys keep their positions: ordering
     * is by position, so the gap is harmless.
     */
    public function handle(Journey $journey): void
    {
        DB::transaction(fn () => $journey->delete());
    }
}
