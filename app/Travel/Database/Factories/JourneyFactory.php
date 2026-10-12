<?php

namespace App\Travel\Database\Factories;

use App\Models\Booking;
use App\Travel\Models\Journey;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Journey>
 */
class JourneyFactory extends Factory
{
    protected $model = Journey::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'booking_id' => Booking::factory(),
            'label' => 'Heenreis',
            'date' => fake()->dateTimeBetween('now', '+6 months'),
            'position' => 0,
        ];
    }
}
