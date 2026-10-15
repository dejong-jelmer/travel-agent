<?php

namespace App\Travel\Actions;

use App\Models\Booking;
use App\Travel\Actions\Concerns\AssignsNextPosition;
use App\Travel\Models\Stay;
use Illuminate\Support\Facades\DB;

class CreateStay
{
    use AssignsNextPosition;

    /**
     * Add a stay after the booking's last one.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function handle(Booking $booking, array $attributes): Stay
    {
        return DB::transaction(fn () => $booking->stays()->create([
            ...$attributes,
            'position' => $this->nextPosition($booking->stays()),
        ]));
    }
}
