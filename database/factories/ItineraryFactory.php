<?php

namespace Database\Factories;

use App\Enums\Meal;
use App\Enums\Transport;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Itinerary>
 */
class ItineraryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->randomElement([
                'Aankomst in ',
                'Verblijf in ',
                'Vertrek uit ',
            ]).fake()->city,
            'description' => fake()->paragraph(3),
            'location' => fake()->city.', '.fake()->country,
            'activities' => fake()->optional()->randomElements([
                'Check-in hotel',
                'Stadswandeling',
                'Museumbezoek',
                'Fietstocht',
                'Boottocht',
            ], rand(0, 4)),
            'accommodation' => fake()->company.' Hotel',
            'meals' => fake()->optional()->randomElements(Meal::cases(), rand(0, 2)),
            'transport' => fake()->optional()->randomElements(Transport::cases(), rand(0, 4)),
            'remark' => fake()->optional()->randomElement([
                'Museum toegang niet inbegrepen in de prijs',
                'Lunch niet inbegrepen',
                'Tickets moeten voor 08:00 ophaald worden op de locatie',
                'Museumbezoek is optioneel',
            ]),
        ];
    }
}
