<?php

namespace Database\Factories;

use App\Enums\TestimonialRelationship;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Testimonial>
 */
class TestimonialFactory extends Factory
{
    /**
     * Define the model's default state: an unpublished, general testimonial by a customer, without a full account.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'trip_id' => null,
            'quote' => fake()->text(200),
            'body' => null,
            'author_name' => fake()->name(),
            'relationship' => TestimonialRelationship::Customer,
            'author_context' => null,
            'travelled_on' => null,
            'link_url' => null,
            'link_label' => null,
            'is_published' => false,
            'sort_order' => 0,
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => [
            'is_published' => true,
        ]);
    }

    public function personal(): static
    {
        return $this->state(fn () => [
            'relationship' => TestimonialRelationship::Personal,
            'author_context' => 'Mijn vader, over zijn reis naar de Vézère-vallei',
        ]);
    }

    public function withBody(): static
    {
        return $this->state(fn () => [
            'body' => fake()->paragraph()."\n\n".fake()->paragraph(),
        ]);
    }
}
