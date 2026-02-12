<?php

namespace Database\Seeders;

use App\Models\Destination;
use App\Services\DestinationService;
use Illuminate\Database\Seeder;

class DestinationSeeder extends Seeder
{
    /**
     * Seed European destinations for sustainable train travel
     */
    public function run(): void
    {
        foreach (DestinationService::europeanDestinations() as $destination) {
            Destination::factory()->withTravelInfo()->create([
                'country_code' => $destination['code'],
                'region' => in_array($destination['name'], ['Engeland, Schotland, Wales']) ? $destination['name'] : null,
                'name' => $destination['name'],
            ]);
        }
    }
}
