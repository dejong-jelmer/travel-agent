<?php

namespace Database\Factories;

use App\Enums\Destination\TravelInfo;
use App\Models\Destination;
use App\Services\DestinationService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Destination>
 */
class DestinationFactory extends Factory
{
    /**
     * Define the model's default state.
     * Returns a unique European destination from DestinationService
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $destination = DestinationService::uniqueRandom();

        return [
            'country_code' => $destination['country_code'],
            'region' => $destination['region'],
            'name' => $destination['name'],
        ];
    }

    /**
     * Create a destination with a specific name
     */
    public function withName(string $destinationName): static
    {
        return $this->state(fn () => [
            'name' => $destinationName,
        ]);
    }

    /**
     * Update destination with travel infromation with section headers
     * defined in App\Enums\Destination\TravelInfo
     */
    public function withTravelInfo(): static
    {
        return $this->afterCreating(function (Destination $destination) {
            $destination->update([
                'travel_info' => collect(TravelInfo::cases())
                    ->mapWithKeys(fn($case) => [
                        $case->value => fake()->text(fake()->numberBetween(50, 250))
                    ])
                    ->all()
            ]);
        });
    }
}
