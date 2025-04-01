<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Image>
 */
class ImageFactory extends Factory
{
    const PATHS = [
        'Dag7-Flamtrein-kortetreinreisnoorwegenzomer9-500x500',
        'Kortetreinreisschotland-1-768x488',
        'Kortetreinreiszwitserland9-1-768x488',
        'langetreinreiszwitserland-1-768x488',
        'treinreisitalieoostenrijk-6-e1678025053471',
        'langetreinreiszwitserland-12-768x488',
        'praag-groot-768x488',
        'Premium-Zwitserland-Gornergrat-3-50',
        'treinreisitalieoostenrijk-6-e1678025053471',
        'Treinreisspanjeportugal21-1-2-768x512',
        'Treinreistoscanecinqueterre10-1-768x488',
        'Verona-Opreisnl-Ciaotutti-768x488',
        'Visit-Sopot-Hotel-Sofitel-Grand-Sopot-_DSC1934m1_e-1200x801-1187768066',
    ];
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'path' => fake()->randomElement(self::PATHS) . '.jpg',
            'featured' => false,
        ];
    }
}
