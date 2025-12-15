<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Locale;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Country>
 */
class CountryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->country(),
        ];
    }

    public function fromLocale(string $locale = 'nl_NL'): static
    {
        return $this->state([
            'name' => Locale::getDisplayRegion($locale, locale_get_default()),
        ]);
    }
}
