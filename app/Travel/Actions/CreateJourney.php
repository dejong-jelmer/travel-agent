<?php

namespace App\Travel\Actions;

use App\Models\Booking;
use App\Travel\Actions\Concerns\AssignsNextPosition;
use App\Travel\Models\Journey;
use Illuminate\Support\Facades\DB;

class CreateJourney
{
    use AssignsNextPosition;

    /**
     * Add a journey after the booking's last one.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function handle(Booking $booking, array $attributes): Journey
    {
        return DB::transaction(fn () => $booking->journeys()->create([
            ...$attributes,
            'position' => $this->nextPosition($booking->journeys()),
        ]));
    }
}
