<?php

namespace Database\Factories;

use App\Enums\Newsletter\CampaignStatus;
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
        $status = fake()->randomElement(CampaignStatus::cases());

        return [
            'subject' => fake()->text(rand(40, 70)),
            'content' => fake()->paragraphs(rand(5, 10), true),
            'preview_text' => fake()->text(rand(140, 255)),
            'status' => $status,
            'scheduled_at' => $status === CampaignStatus::Scheduled ? now()->startOfHour()->addDays(rand(1, 10))->addHours(rand(0, 24)) : null,
            'sent_at' => $status === CampaignStatus::Sent ? now()->startOfHour()->subDays(rand(1, 10))->subHours(rand(0, 24)) : null,
            'sent_count' => $status === CampaignStatus::Sent ? rand(1000, 10000) : 0,
        ];
    }
}
