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
            'activities' => fake()->optional()->randomElements(self::ACTIVITIES, fake()->numberBetween(1, 4)),
            'accommodation' => fake()->company().' Hotel',
            'meals' => fake()->optional()->randomElements(Meal::cases(), fake()->numberBetween(1, 2)),
            'transport' => fake()->optional()->randomElements(Transport::cases(), fake()->numberBetween(1, 4)),
            'remark' => fake()->optional()->randomElement(self::REMARKS),
        ];
    }

    public function withImage(): static
    {
        return $this->has(
            Image::factory()->primary(),
            ImageRelation::Image->value
        );
    }
}
