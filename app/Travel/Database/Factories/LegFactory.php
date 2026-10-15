<?php

namespace App\Travel\Database\Factories;

use App\Travel\Enums\LegType;
use App\Travel\Models\Accommodation;
use App\Travel\Models\Journey;
use App\Travel\Models\Leg;
use App\Travel\Models\Station;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * A leg of a journey by default. For an arrival route, use accommodationRoute() or ->for($accommodation, 'legable').
 *
 * @extends Factory<Leg>
 */
class LegFactory extends Factory
{
    protected $model = Leg::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'legable_type' => (new Journey)->getMorphClass(),
            'legable_id' => Journey::factory(),
            'type' => LegType::Train,
            'position' => 0,
        ];
    }

    /**
     * A train between two stations, timed, so without a stored duration.
     */
    public function train(): static
    {
        return $this->state(function () {
            $departure = CarbonImmutable::instance(fake()->dateTimeBetween('+1 week', '+6 months'))
                ->setTime(fake()->numberBetween(6, 18), 0);

            return [
                'type' => LegType::Train,
                'from_station_id' => Station::factory(),
                'to_station_id' => Station::factory(),
                'departure_at' => $departure,
                'arrival_at' => $departure->addMinutes(fake()->numberBetween(30, 240)),
                'duration_minutes' => null,
            ];
        });
    }

    /**
     * A transfer within one station, with a duration but no times.
     */
    public function transfer(): static
    {
        return $this->state(fn () => [
            'type' => LegType::Transfer,
            'from_station_id' => Station::factory(),
            'to_station_id' => fn (array $attributes) => $attributes['from_station_id'],
            'departure_at' => null,
            'arrival_at' => null,
            'duration_minutes' => fake()->numberBetween(5, 45),
        ]);
    }

    public function walk(): static
    {
        return $this->state(fn () => [
            'type' => LegType::Walk,
            'from_station_id' => null,
            'to_station_id' => null,
            'departure_at' => null,
            'arrival_at' => null,
            'duration_minutes' => fake()->numberBetween(2, 15),
        ]);
    }

    /**
     * A leg of an accommodation's arrival route: reused for every booking, so without times.
     */
    public function accommodationRoute(): static
    {
        return $this->state(fn () => [
            'legable_type' => (new Accommodation)->getMorphClass(),
            'legable_id' => Accommodation::factory(),
            'departure_at' => null,
            'arrival_at' => null,
        ]);
    }
}
