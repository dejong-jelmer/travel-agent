<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BookingContact>
 */
class BookingContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'street' => fake()->streetname(),
            'house_number' => fake()->randomNumber(3, false),
            'addition' => fake()->streetSuffix(),
            'postal_code' => fake()->postcode(),
            'city' => fake()->city(),
            'email' => fake()->email(),
            'phone' => fake()->phoneNumber(),
        ];
    }
}
