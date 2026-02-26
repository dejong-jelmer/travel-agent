<?php

namespace Database\Factories;

use App\Enums\ImageRelation;
use App\Enums\Meal;
use App\Enums\Transport;
use App\Models\Image;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Itinerary>
 */
class ItineraryFactory extends Factory
{
    private const TITLE_PREFIXES = ['Aankomst in ', 'Verblijf in ', 'Vertrek uit '];

    private const ACTIVITIES = ['Check-in hotel', 'Stadswandeling', 'Museumbezoek', 'Fietstocht', 'Boottocht'];

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
            'location' => fake()->city().', '.fake()->country(),
            'accommodation' => fake()->company().' '.'Hotel',
        ];
    }

    public function withImage(): static
    {
        return $this->has(
            Image::factory()->primary(),
            ImageRelation::Image->value
        );
    }

    public function withMeals(): static
    {
        return $this->state(fn (array $attributes) => [
            'meals' => fake()->optional()->randomElements(Meal::cases(), fake()->numberBetween(1, 2)) ?? [],
        ]);
    }

    public function withTransport(): static
    {
        return $this->state(fn (array $attributes) => [
            'transport' => fake()->randomElements([Transport::Train, Transport::Ferry], fake()->numberBetween(1, 2)),
        ]);
    }

    public function withRemarks(): static
    {
        return $this->state(fn (array $attributes) => [
            'remark' => fake()->optional()->randomElement(self::REMARKS),
        ]);
    }

    public function withActivities(): static
    {
        return $this->state(fn (array $attributes) => [
            'activities' => fake()->optional()->randomElements(self::ACTIVITIES, fake()->numberBetween(1, 4)) ?? [],
        ]);
    }
}
