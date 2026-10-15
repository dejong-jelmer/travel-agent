<?php

namespace App\Travel\Database\Factories;

use App\Travel\Models\City;
use App\Travel\Models\Station;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Station>
 */
class StationFactory extends Factory
{
    protected $model = Station::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'city_id' => City::factory(),
            'name' => 'Station '.fake()->city(),
        ];
    }
}
