<?php

namespace Database\Factories;

use App\Enums\Trip\TripFormat;
use App\Models\Departure;
use App\Models\Trip;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Departure>
 */
class DepartureFactory extends Factory
{
    /**
     * Define the model's default state: an open departure of a week, two months from now.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $departsOn = Carbon::instance(fake()->dateTimeBetween('+2 months', '+8 months'))->startOfDay();
        $returnsOn = $departsOn->copy()->addDays(7);

        return [
            'trip_id' => Trip::factory()->state(['format' => TripFormat::Group]),
            'departs_on' => $departsOn,
            'returns_on' => $returnsOn,
            'min_participants' => 6,
            'max_participants' => 12,
            'price_per_person' => fake()->numberBetween(899, 1999) * 100,
            'single_supplement' => fake()->numberBetween(150, 500) * 100,
            // From the dates the departure ends up with, so a test that sets its own dates gets a matching deadline
            'decision_date' => fn (array $attributes) => Departure::latestDecisionDateFor(
                Carbon::parse($attributes['departs_on']),
                Carbon::parse($attributes['returns_on'])
            ),
        ];
    }

    public function guaranteed(): static
    {
        return $this->state(['guaranteed_at' => now()]);
    }

    public function cancelled(string $reason = 'Too few participants'): static
    {
        return $this->state(['cancelled_at' => now(), 'cancellation_reason' => $reason]);
    }
}
