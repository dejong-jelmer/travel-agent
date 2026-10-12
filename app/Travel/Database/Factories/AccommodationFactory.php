<?php

namespace App\Travel\Database\Factories;

use App\Travel\Enums\AccommodationType;
use App\Travel\Models\Accommodation;
use App\Travel\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Accommodation>
 */
class AccommodationFactory extends Factory
{
    protected $model = Accommodation::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'city_id' => City::factory(),
            'name' => 'Hotel '.fake()->lastName(),
            'type' => AccommodationType::Hotel,
            'address' => fake()->streetAddress(),
            'phone' => fake()->phoneNumber(),
            'check_in' => '15:00',
            'check_out' => '11:00',
        ];
    }
}
