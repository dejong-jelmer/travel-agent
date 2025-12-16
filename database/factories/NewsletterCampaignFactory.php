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
        $status = fake()->randomElement([
            CampaignStatus::Draft,
            CampaignStatus::Scheduled,
            CampaignStatus::Sent,
        ]);

        $recipients = in_array($status, [CampaignStatus::Sent]) ? rand(1000, 10000) : 0;

        return [
            'subject' => fake()->text(rand(40, 70)),
            'content' => fake()->paragraphs(rand(5, 10), true),
            'preview_text' => fake()->text(rand(140, 255)),
            'status' => $status,
            'scheduled_at' => in_array($status, [CampaignStatus::Scheduled, CampaignStatus::Sent])
                ? now()->startOfHour()->addDays(rand(1, 10))->addHours(rand(0, 24))
                : null,
            'sent_at' => in_array($status, [CampaignStatus::Sent])
                ? now()->startOfHour()->subDays(rand(1, 10))->subHours(rand(0, 24))
                : null,
            'sent_count' => $recipients,
            'total_recipients' => $recipients,
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
        return $this->state(fn () => [
            'status' => CampaignStatus::Scheduled,
            'scheduled_at' => now()->startOfHour()->addDays(rand(1, 10))->addHours(rand(0, 24)),

        ]);
    }

    public function sent(): static
    {
        $recipients = rand(1000, 10000);

        return $this->state(fn () => [
            'status' => CampaignStatus::Sent,
            'scheduled_at' => now()->startOfHour()->addDays(rand(1, 10))->addHours(rand(0, 24)),
            'sent_at' => now()->startOfHour()->subDays(rand(1, 10))->subHours(rand(0, 24)),
            'sent_count' => $recipients,
            'total_recipients' => $recipients,
        ]);
    }
}
