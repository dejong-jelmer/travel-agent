<?php

namespace Database\Factories;

use App\Enums\TripRequest\Status;
use App\Models\Trip;
use App\Models\TripRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TripRequest>
 */
class TripRequestFactory extends Factory
{
    protected $model = TripRequest::class;

    public function definition(): array
    {
        $year = (int) date('Y') + 1;
        $month = str_pad((string) fake()->numberBetween(1, 12), 2, '0', STR_PAD_LEFT);

        return [
            'trip_id' => Trip::factory(),
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->optional()->phoneNumber(),
            'preferred_month' => fake()->optional()->passthrough($year.'-'.$month),
            'preferred_period_note' => fake()->optional()->sentence(),
            'travelers_count' => fake()->numberBetween(1, 9),
            'departure_station' => fake()->optional()->city(),
            'notes' => fake()->optional()->paragraph(),
            'status' => Status::New,
            'consent_privacy' => true,
            'consent_privacy_at' => now(),
        ];
    }

    public function contacted(): static
    {
        return $this->state(fn () => ['status' => Status::Contacted]);
    }

    public function converted(): static
    {
        return $this->state(fn () => ['status' => Status::Converted]);
    }

    public function archived(): static
    {
        return $this->state(fn () => ['status' => Status::Archived]);
    }
}
