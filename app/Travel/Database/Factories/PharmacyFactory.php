<?php

namespace App\Travel\Database\Factories;

use App\Travel\Models\City;
use App\Travel\Models\Pharmacy;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pharmacy>
 */
class PharmacyFactory extends Factory
{
    protected $model = Pharmacy::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'city_id' => City::factory(),
            'name' => 'Pharmacie '.fake()->lastName(),
            'address' => fake()->streetAddress(),
        ];
    }
}
