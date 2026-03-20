<?php

namespace Tests\Feature;

use App\Models\Destination;
use App\Models\Trip;
use App\Services\CountryService;
use Database\Seeders\CountrySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CountryServiceTest extends TestCase
{
    use RefreshDatabase;

    private CountryService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CountrySeeder::class);
        $this->service = new CountryService;
    }

    public function test_returns_empty_array_when_trips_have_no_destinations(): void
    {
        $trips = Trip::factory()->count(2)->create()->load('destinations.country');

        $result = $this->service->getCountriesForTrips($trips);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function test_returns_plain_array_not_nested_under_data_key(): void
    {
        $destination = Destination::factory()->create();
        $trip = Trip::factory()->create();
        $trip->destinations()->attach($destination);

        $trips = Trip::with('destinations.country')->get();
        $result = $this->service->getCountriesForTrips($trips);

        $this->assertIsArray($result);
        $this->assertArrayNotHasKey('data', $result);
    }

    public function test_result_contains_expected_keys(): void
    {
        $destination = Destination::factory()->create();
        $trip = Trip::factory()->create();
        $trip->destinations()->attach($destination);

        $trips = Trip::with('destinations.country')->get();
        $result = $this->service->getCountriesForTrips($trips);

        $this->assertCount(1, $result);
        $this->assertArrayHasKey('code', $result[0]);
        $this->assertArrayHasKey('name', $result[0]);
        $this->assertArrayHasKey('en_name', $result[0]);
        $this->assertSame($destination->country_code, $result[0]['code']);
    }

    public function test_deduplicates_countries_from_multiple_trips(): void
    {
        $destination = Destination::factory()->create();

        $trip1 = Trip::factory()->create();
        $trip1->destinations()->attach($destination);

        $trip2 = Trip::factory()->create();
        $trip2->destinations()->attach($destination);

        $trips = Trip::with('destinations.country')->get();
        $result = $this->service->getCountriesForTrips($trips);

        $this->assertCount(1, $result);
        $this->assertSame($destination->country_code, $result[0]['code']);
    }

    public function test_returns_countries_sorted_by_translated_name(): void
    {
        $destFR = Destination::factory()->create(['country_code' => 'FR']);
        $destIT = Destination::factory()->create(['country_code' => 'IT']);

        $tripFR = Trip::factory()->create();
        $tripFR->destinations()->attach($destFR);

        $tripIT = Trip::factory()->create();
        $tripIT->destinations()->attach($destIT);

        $trips = Trip::with('destinations.country')->get();
        $result = $this->service->getCountriesForTrips($trips);

        $this->assertCount(2, $result);

        $names = array_column($result, 'name');
        $sorted = $names;
        sort($sorted);
        $this->assertSame($sorted, $names, 'Countries should be sorted alphabetically by translated name.');
    }

    public function test_filters_destinations_without_a_linked_country(): void
    {
        // 'XX' does not exist in the countries table → $dest->country returns null → filtered out
        $destination = Destination::factory()->create(['country_code' => 'XX']);
        $trip = Trip::factory()->create();
        $trip->destinations()->attach($destination);

        $trips = Trip::with('destinations.country')->get();
        $result = $this->service->getCountriesForTrips($trips);

        $this->assertEmpty($result);
    }
}
