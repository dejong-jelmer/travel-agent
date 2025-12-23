<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NewsletterSubscriber>
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
            'email' => fake()->unique()->email(),
            'name' => fake()->name(),
            'token' => bin2hex(random_bytes(32)),
            'confirmation_token' => bin2hex(random_bytes(32)),
            'unsubscribe_token' => bin2hex(random_bytes(32)),
            'confirmed_at' => fake()->optional(0.8)->dateTime(),
            'confirmation_expires_at' => now()->addHours(config('newsletter.subscription.confirmation_expires_after')),
            'subscribed_at' => fake()->dateTime(),
            'unsubscribed_at' => fake()->optional(0.2)->dateTime(),
        ];
    }

    public function active(): static
    {
        return $this->state([
            'confirmed_at' => now(),
            'unsubscribed_at' => null,
        ]);
    }

    public function pending(): static
    {
        return $this->state([
            'confirmed_at' => null,
            'unsubscribed_at' => null,
        ]);
    }

    public function Expired(): static
    {
        return $this->state([]);
    }

    public function unsubscribed(): static
    {
        return $this->state([
            'confirmed_at' => now(),
            'unsubscribed_at' => now(),
        ]);
    }
}
