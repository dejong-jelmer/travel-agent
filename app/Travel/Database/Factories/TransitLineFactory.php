<?php

namespace App\Travel\Database\Factories;

use App\Travel\Enums\TransitMode;
use App\Travel\Models\TransitLine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TransitLine>
 */
class TransitLineFactory extends Factory
{
    protected $model = TransitLine::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'mode' => TransitMode::Train,
            'name' => fake()->bothify('Intercity ###'),
            'color' => null,
            'info_link' => null,
        ];
    }
}
