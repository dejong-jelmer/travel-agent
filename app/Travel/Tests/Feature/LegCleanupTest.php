<?php

namespace App\Travel\Tests\Feature;

use App\Models\Booking;
use App\Travel\Enums\AccommodationType;
use App\Travel\Enums\LegType;
use App\Travel\Enums\TransitMode;
use App\Travel\Models\Accommodation;
use App\Travel\Models\City;
use App\Travel\Models\TransitLine;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LegCleanupTest extends TestCase
{
    use RefreshDatabase;

    public function test_deleting_a_journey_deletes_its_legs_and_their_transit_lines(): void
    {
        $journey = Booking::factory()->create()->journeys()->create(['label' => 'Heenreis']);
        $tram = $journey->legs()->create(['type' => LegType::Tram]);
        $tram->transitLines()->attach([
            $this->transitLine('Tram C')->id => ['direction' => 'Parc des Expositions', 'position' => 0],
            $this->transitLine('Tram D')->id => ['direction' => 'Hippodrome', 'position' => 1],
        ]);
        $journey->legs()->create(['type' => LegType::Walk, 'position' => 1]);
        // Shares its legable_id with the journey, so only the morph type keeps it apart.
        $arrivalRouteLeg = Accommodation::factory()->create(['id' => $journey->id])->legs()->create(['type' => LegType::Walk]);

        $journey->delete();

        $this->assertDatabaseCount('travel_legs', 1);
        $this->assertModelExists($arrivalRouteLeg);
        $this->assertDatabaseCount('travel_leg_transit_line', 0);
    }

    public function test_deleting_an_accommodation_deletes_its_arrival_route(): void
    {
        $accommodation = $this->accommodation();
        $accommodation->legs()->create(['type' => LegType::Tram])
            ->transitLines()->attach($this->transitLine('Tram B'), ['direction' => 'Berges de la Garonne']);

        $accommodation->delete();

        $this->assertDatabaseCount('travel_legs', 0);
        $this->assertDatabaseCount('travel_leg_transit_line', 0);
    }

    public function test_an_accommodation_with_a_stay_keeps_its_arrival_route_when_the_delete_is_refused(): void
    {
        $accommodation = $this->accommodation();
        $leg = $accommodation->legs()->create(['type' => LegType::Walk]);
        Booking::factory()->create()->stays()->create(['accommodation_id' => $accommodation->id]);

        $this->assertThrows(fn () => $accommodation->delete(), QueryException::class);

        $this->assertModelExists($accommodation);
        $this->assertModelExists($leg);
    }

    private function accommodation(): Accommodation
    {
        return Accommodation::create([
            'city_id' => City::create(['name' => 'Bordeaux', 'country' => 'FR'])->id,
            'name' => "Guest house L'Esprit des Chartrons",
            'type' => AccommodationType::GuestHouse,
        ]);
    }

    private function transitLine(string $name): TransitLine
    {
        return TransitLine::create(['mode' => TransitMode::Tram, 'name' => $name]);
    }
}
