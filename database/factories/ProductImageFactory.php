<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductImage>
 */
class ProductImageFactory extends Factory
{
    const IMG_PATHS = [
        '1S4tZTviiXDlj0HCMqrRWoa5vLx9BagT3VtphAgI',
        '4QKM25pYXvLegNE8fE6fGLvVo40IaMc2iUbqDk3z',
        '76HX9hUCKtjOXCWdk9dHF3Iy7ym5FgzbGw6HcAvd',
        'cwsINssRyksqPo3lpXWtWMPghbhx0AFje8rhkOvq',
        'jhR6zjRvRxmbKYy7OZcZqu8mUw05B7TqWqSViRsK',
        'LiVa6mfL4yBffhhivXSUTc5TdWDkW5QoYCNTOMQc',
        'lMSqxgnHFJIYa4EH2qSEWYcKf5tIGu3hCW0csaG1',
        'N01ZoCd0Y1rD8VglIjD0QXTmahDxD6N7x4dbNpo6',
        'PtF7P5W2SIkDsytMCj0FJ2FeJzoGkTOXjrOjdokD',
        'Tp7Q8MtASbcY1MDoaQTIdmp4Vf6FXQqf1eRSlMre',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $path = fake()->randomElement(self::IMG_PATHS);

        return [
            'path' => "images/products/{$path}.jpg",
        ];
    }
}
