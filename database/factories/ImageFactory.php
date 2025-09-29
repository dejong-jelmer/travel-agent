<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Image>
 */
class ImageFactory extends Factory
{
    const PATHS = [
        'cinque-terre-2928833_1280.jpg',
        'dolomites-2897602_1280.jpg',
        'hill-5324149_1280.jpg',
        'house-4028391_1280.jpg',
        'houses-4093227_1280.jpg',
        'hut-9582608_1280.jpg',
        'konigssee-7276585_1280.jpg',
        'landscape-5104510_1280.jpg',
        'mountainous-5942962_1280.jpg',
        'mountains-5237939_1280.jpg',
        'neuschwanstein-2602208_1280.jpg',
        'river-4336788_1280.jpg',
        'trees-7662375_1280.jpg',
        'water-8100724_1280.jpg',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'path' => fake()->randomElement(self::PATHS),
            'featured' => false,
        ];
    }
}
