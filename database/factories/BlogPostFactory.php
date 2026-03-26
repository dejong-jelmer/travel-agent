<?php

namespace Database\Factories;

use App\Enums\BlogPost\Status;
use App\Enums\ImageRelation;
use App\Models\Image;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BlogPost>
 */
class BlogPostFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->sentence();

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'body' => fake()->paragraphs(3, true),
            'excerpt' => fake()->text(200),
            'status' => Status::Draft,
            'published_at' => null,
            'meta_title' => fake()->text(55),
            'meta_description' => fake()->text(155),
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => [
            'status' => Status::Published,
            'published_at' => now()->subDay(),
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn () => [
            'status' => Status::Draft,
            'published_at' => null,
        ]);
    }

    public function scheduled(): static
    {
        return $this->state(fn () => [
            'status' => Status::Published,
            'published_at' => now()->addWeek(),
        ]);
    }

    public function withHeroImage(): static
    {
        return $this->has(
            Image::factory()->primary(),
            ImageRelation::HeroImage->value
        );
    }
}
