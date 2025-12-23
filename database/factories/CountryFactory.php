<?php

namespace Database\Factories;

use App\Services\CountryService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Country>
 */
class CountryFactory extends Factory
{
    /**
     * Define the model's default state.
     * Returns a unique European country from CountryService
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => CountryService::uniqueRandomName(),
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
