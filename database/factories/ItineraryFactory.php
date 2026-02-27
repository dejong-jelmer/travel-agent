<?php

namespace Database\Factories;

use App\Enums\ImageRelation;
use App\Models\Image;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\Sequence;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Itinerary>
 */
class ItineraryFactory extends Factory
{
    private const TITLE_PREFIXES = ['Aankomst in ', 'Verblijf in ', 'Vertrek uit '];

    private const ACTIVITIES = ['Stadswandeling', 'Museumbezoek', 'Fietstocht', 'Boottocht'];

    private const REMARKS = [
        'Museum toegang niet inbegrepen in de prijs',
        'Lunch niet inbegrepen',
        'Tickets moeten voor 08:00 opgehaald worden op de locatie',
        'Museumbezoek is optioneel',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->randomElement(self::TITLE_PREFIXES).fake()->city(),
            'description' => fake()->paragraph(3),
            'day_from' => 1,
            'day_to' => null,
            'accommodation' => fake()->company().' '.'Hotel',
        ];
    }

    public function withIncrementingDays(): static
    {
        return $this->sequence(
            fn (Sequence $sequence) => ['day_from' => $sequence->index + 1]
        );
    }

    public function withIncrementingOrder(): static
    {
        return $this->sequence(
            fn (Sequence $sequence) => ['order' => $sequence->index + 1]
        );
    }

    public function withImage(): static
    {
        return $this->has(
            Image::factory()->primary(),
            ImageRelation::Image->value
        );
    }

    public function withRemarks(): static
    {
        return $this->state(fn (array $attributes) => [
            'remark' => fake()->optional(0.1)->randomElement(self::REMARKS),
        ]);
    }

    public function withActivities(): static
    {
        return $this->state(fn (array $attributes) => [
            'activities' => fake()->optional()->randomElements(self::ACTIVITIES, fake()->numberBetween(1, 4)) ?? [],
        ]);
    }
}
