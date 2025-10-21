<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class NewsletterSubscriberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'name' => fake()->name(),
            'subscribed_at' => now(),
            'confirmed_at' => null,
            'confirmation_expires_at' => now()->addHours(24),
            'unsubscribed_at' => null,
        ];
    }

    public function expired(): static
    {
        return $this->state(fn(array $attributes) => [
            'confirmation_expires_at' => now()->subHour(),
        ]);
    }
}
