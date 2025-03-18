<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Country;
use PHPUnit\Framework\Constraint\Count;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    const IMG_PATHS = [
        '0CVO5DSMb45wY5Gce6OmDFtObj7C6IYqhVDc1O3G',
        '0atWniYhuFME7Eam8Xs5voQCuH2qKfVTrUqMqWSm',
        'xBdb4Xf3B1rrgtNqgBMn2BKrPHwRcs9suliMoVE6',
    ];
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
        $path = fake()->randomElement(self::IMG_PATHS);
        return [
            'name' => $city,
            'slug' => Str::slug("bijzondere-reis-naar-{$city}-{$country->name}"),
            'description' => "Mooie reis prachtige reis, waar u het mooie {$city} bezoek in {$country->name}. {$text}",
            'price' => fake()->randomFloat(2, 200, 1200),
            'duration' => fake()->randomDigit(),
            'image' => "images/products/featured/{$path}.jpg",
            'active' => true,
            'featured' => true,
            'published_at' => now(),
        ];
    }
}
