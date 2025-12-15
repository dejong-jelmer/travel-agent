<?php

namespace Database\Factories;

use App\Enums\ImageRelation;
use App\Enums\Newsletter\CampaignStatus;
use App\Models\Image;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NewsletterCampaign>
 */
class NewsletterCampaignFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'subject' => fake()->text(rand(40, 70)),
            'content' => fake()->paragraphs(rand(5, 10), true),
            'preview_text' => fake()->text(rand(140, 255)),
            'status' => CampaignStatus::Draft,
        ];
    }

    public function withHeroImage(): static
    {
        return $this->has(
            Image::factory()->primary(),
            ImageRelation::HeroImage->value
        );
    }

    public function scheduled(): static
    {
        return $this->state([
            'status' => CampaignStatus::Scheduled,
            'scheduled_at' => now()->startOfHour()->addDays(rand(1, 10))->addHours(rand(0, 24)),

        ]);
    }

    public function sent(): static
    {
        return $this->state([
            'status' => CampaignStatus::Sent,
            'scheduled_at' => now()->startOfHour()->addDays(rand(1, 10))->addHours(rand(0, 24)),
            'sent_at' => now()->startOfHour()->subDays(rand(1, 10))->subHours(rand(0, 24)),
            'sent_count' => rand(1000, 10000),
            'total_recipients' => 0,
            'sent_count' => 0,
        ]);
    }
}
