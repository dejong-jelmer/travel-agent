<?php

namespace Database\Factories;

use App\Enums\Trip\PriceLabel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TripPrice>
 */
class TripPriceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $validFrom = now();
        $validUntil = now()->addMonths(12);

        return [
            'valid_from' => $validFrom->format('Y-m-d'),
            'valid_until' => $validUntil->format('Y-m-d'),
            'base_price_pp' => fake()->numberBetween(899, 1999) * 100,
            'single_supplement' => fake()->numberBetween(150, 500) * 100,
            'label' => fake()->randomElement(PriceLabel::cases())->value,
        ];
    }
}
