<?php

namespace Database\Factories;

use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $country = Country::inRandomOrder()->first();
        while ($country === null) {
            Country::factory()->create();
            $country = Country::inRandomOrder()->first();
        }
        $city = fake()->city();
        $text = fake()->paragraph();
        $duration = fake()->randomDigit();
        while($duration < 4) {
            $duration = fake()->randomDigit();
        }

        return [
            'name' => $city,
            'slug' => Str::slug("bijzondere-reis-naar-{$city}-{$country->name}"),
            'description' => "Mooie reis prachtige reis, waar u het mooie {$city} bezoek in {$country->name}. {$text}",
            'price' => randomPrice(),
            'duration' => $duration,
            'active' => true,
            'featured' => true,
            'published_at' => now(),
        ];
    }
}
