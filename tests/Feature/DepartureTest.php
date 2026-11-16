<?php

namespace Tests\Feature;

use App\Enums\Trip\TripFormat;
use App\Models\Departure;
use App\Models\Trip;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepartureTest extends TestCase
{
    use RefreshDatabase;

    public function test_departure_belongs_to_a_trip_that_lists_its_departures_by_date(): void
    {
        $trip = Trip::factory()->create(['format' => TripFormat::Group]);
        $later = Departure::factory()->for($trip)->create(['departs_on' => '2027-09-01', 'returns_on' => '2027-09-08']);
        $sooner = Departure::factory()->for($trip)->create(['departs_on' => '2027-05-01', 'returns_on' => '2027-05-08']);
        Departure::factory()->create();

        $this->assertTrue($sooner->trip->is($trip));
        $this->assertSame([$sooner->id, $later->id], $trip->departures->modelKeys());
    }

    public function test_departure_keeps_its_trip_when_the_trip_is_soft_deleted(): void
    {
        $departure = Departure::factory()->create();
        $departure->trip->delete();

        $this->assertNotNull($departure->refresh()->trip);
    }

    public function test_departure_without_a_decision_date_gets_the_latest_one_allowed(): void
    {
        $trip = Trip::factory()->create(['format' => TripFormat::Group]);

        $departure = $trip->departures()->create([
            'departs_on' => '2027-06-20',
            'returns_on' => '2027-06-26',
            'min_participants' => 6,
            'max_participants' => 12,
        ]);

        $this->assertSame('2027-05-31', $departure->refresh()->decision_date->toDateString());
    }

    public function test_departure_keeps_an_earlier_decision_date_of_its_own(): void
    {
        $departure = Departure::factory()->create([
            'departs_on' => '2027-06-20',
            'returns_on' => '2027-06-26',
            'decision_date' => '2027-05-01',
        ]);

        $departure->update(['max_participants' => 14]);

        $this->assertSame('2027-05-01', $departure->refresh()->decision_date->toDateString());
    }

    public function test_departure_stores_prices_in_cents_and_falls_back_on_null(): void
    {
        $priced = Departure::factory()->create(['price_per_person' => 129500, 'single_supplement' => 0]);
        $unpriced = Departure::factory()->create(['price_per_person' => null, 'single_supplement' => null]);

        $this->assertSame(129500, $priced->refresh()->price_per_person);
        $this->assertSame(0, $priced->single_supplement);
        $this->assertNull($unpriced->refresh()->price_per_person);
        $this->assertNull($unpriced->single_supplement);
    }

    public function test_factory_states_mark_a_departure_guaranteed_or_cancelled(): void
    {
        $open = Departure::factory()->create();
        $guaranteed = Departure::factory()->guaranteed()->create();
        $cancelled = Departure::factory()->cancelled('Strike')->create();

        $this->assertNull($open->guaranteed_at);
        $this->assertNull($open->cancelled_at);
        $this->assertNotNull($guaranteed->guaranteed_at);
        $this->assertNotNull($cancelled->cancelled_at);
        $this->assertSame('Strike', $cancelled->cancellation_reason);
    }
}
