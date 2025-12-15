<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Image>
 */
class ImageFactory extends Factory
{
    const PATHS = [
        'Les-Eyzies-Dordogne-3379109777.jpeg',
        'Les-Eyzies-de-Tayac-6-754785662.jpg',
        'cinque-terre-2670762_1280.jpg',
        'cinque-terre-2928833_1280.jpg',
        'city-9169729_1280.jpg',
        'det_vezere_lascaux_2_brper0941_br-2319855822.jpg',
        'dolomites-2897602_1280.jpg',
        'duc-nguyen-uTFsH7EF06c-unsplash.jpg',
        'genoa-4948029_1280.jpg',
        'grotte-de-rouffignac-xl-3424735708.jpg',
        'hill-5324149_1280.jpg',
        'house-4028391_1280.jpg',
        'houses-4093227_1280.jpg',
        'hut-9582608_1280.jpg',
        'italy-4090933_1280.jpg',
        'konigssee-7276585_1280.jpg',
        'landscape-4701724_640.jpg',
        'landscape-5104510_1280.jpg',
        'les-eyzies-3-1200x1200-1586214200.jpg',
        'mammothcavefrance2-1476674310.jpg',
        'mountainous-5942962_1280.jpg',
        'mountains-5237939_1280.jpg',
        'neuschwanstein-2602208_1280.jpg',
        'pexels-michael-block-1691617-3225528 1.jpg',
        'river-4336788_1280.jpg',
        'riviere-vezere-1024x640-3362071717.jpg',
        'trees-7662375_1280.jpg',
        'university-5188610_1280.jpg',
        'vernazza-5069291_1280.jpg',
        'village-279013_1280.jpg',
        'water-8100724_1280.jpg',
    ];

    /**
     * Define the model's default state.
     *
     * Uses predefined seed images from PATHS const.
     * New uploads via UI will get hash filenames (handled by ManagesImages trait).
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $dir = config('images.directory');
        $filename = fake()->randomElement(self::PATHS);

        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $hashName = Str::random(40).'.'.$extension;

        Storage::disk(config('images.disk'))->copy(
            '/seed/'.basename($filename),
            $dir.'/'.$hashName
        );

        return [
            'path' => $hashName,
            'original_name' => basename($filename),
            'is_primary' => false,
            'mime_type' => 'image/jpeg',
            'size' => fake()->randomNumber(5, true),
        ];
    }

    public function primary(): static
    {
        return $this->state([
            'is_primary' => true,
        ]);
    }
}
