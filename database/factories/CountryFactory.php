<?php

namespace Database\Factories;

use App\Resources\CountryResource;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Country>
 */
class CountryFactory extends Factory
{
    /**
     * Define the model's default state.
     * Returns a unique European country from CountryResource
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => CountryResource::uniqueRandomName(),
        ];
    }

    /**
     * Create a country with a specific name
     */
    public function withName(string $countryName): static
    {
        return $this->state(fn () => [
            'name' => $countryName,
        ]);
    }
}
