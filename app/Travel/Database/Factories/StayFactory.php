<?php

namespace App\Travel\Database\Factories;

use App\Models\Booking;
use App\Travel\Models\Accommodation;
use App\Travel\Models\Stay;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Stay>
 */
class StayFactory extends Factory
{
    protected $model = Stay::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $arrival = CarbonImmutable::instance(fake()->dateTimeBetween('now', '+6 months'))->startOfDay();

        return [
            'booking_id' => Booking::factory(),
            'accommodation_id' => Accommodation::factory(),
            'arrival' => $arrival,
            'departure' => $arrival->addDays(3),
            'position' => 0,
        ];
    }
}
