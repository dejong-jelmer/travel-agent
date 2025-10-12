<?php

namespace Database\Factories;

use App\Enums\TravelerType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class BookingTravelerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(TravelerType::cases(), rand(0, 1))->value;

        return [
            'type' => $type,
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'birthdate' => $type === TravelerType::Adult->value
                ? fake()->dateTimeBetween('-80 years', '-18 years')->format('Y-m-d')
                : fake()->dateTimeBetween('-12 years', 'now')->format('Y-m-d'),
            'nationality' => fake()->country(),
        ];
    }
}
