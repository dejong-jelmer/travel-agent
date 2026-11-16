<?php

namespace Tests\Feature;

use App\Enums\Booking\Status as BookingStatus;
use App\Enums\Trip\TripFormat;
use App\Models\Booking;
use App\Models\Departure;
use App\Models\Destination;
use App\Models\Trip;
use App\Models\TripPrice;
use Database\Seeders\CountrySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

/**
 * Group trips on the public site.
 */
class GroupTripPublicTest extends TestCase
{
    use RefreshDatabase;

    private Trip $trip;

    protected function setUp(): void
    {
        parent::setUp();

        $this->travelTo('2027-01-10 09:00:00');
        $this->trip = Trip::factory()->withHeroImage()->create(['format' => TripFormat::Group, 'published_at' => '2027-01-01']);
    }

    private function departure(array $attributes = []): Departure
    {
        return Departure::factory()->for($this->trip)->create([
            'departs_on' => '2027-06-20',
            'returns_on' => '2027-06-26',
            'min_participants' => 4,
            'max_participants' => 8,
            'price_per_person' => 120000,
            'single_supplement' => 15000,
            ...$attributes,
        ]);
    }

    private function book(Departure $departure, int $travelers, BookingStatus $status = BookingStatus::Confirmed): Booking
    {
        return Booking::factory()->onDeparture($departure, $travelers)->create(['status' => $status]);
    }

    // Starting price

    public function test_starting_price_of_a_group_trip_is_its_cheapest_departure(): void
    {
        $this->departure(['price_per_person' => 135000]);
        $this->departure(['price_per_person' => 119500, 'departs_on' => '2027-09-01', 'returns_on' => '2027-09-07']);

        $trip = $this->trip->fresh();

        $this->assertSame(119500, $trip->starting_from_price);
        $this->assertSame('1.195', $trip->price_formatted);
        $this->assertFalse($trip->is_expected);
    }

    public function test_starting_price_ignores_the_price_rows_of_a_group_trip_with_priced_departures(): void
    {
        TripPrice::factory()->for($this->trip)->create(['valid_from' => '2027-01-01', 'valid_until' => '2027-12-31', 'base_price_pp' => 50000]);
        $this->departure();

        $this->assertSame(120000, $this->trip->fresh()->starting_from_price);
    }

    public function test_departure_without_its_own_price_counts_with_the_price_of_the_trip_for_its_date(): void
    {
        TripPrice::factory()->for($this->trip)->create(['valid_from' => '2027-01-01', 'valid_until' => '2027-06-30', 'base_price_pp' => 99000]);
        TripPrice::factory()->for($this->trip)->create(['valid_from' => '2027-07-01', 'valid_until' => '2027-12-31', 'base_price_pp' => 50000]);
        $this->departure(['price_per_person' => null]);
        $this->departure(['price_per_person' => 110000, 'departs_on' => '2027-09-01', 'returns_on' => '2027-09-07']);

        $this->assertSame(99000, $this->trip->fresh()->starting_from_price);
    }

    public function test_starting_price_leaves_out_cancelled_and_departed_departures(): void
    {
        $this->departure(['price_per_person' => 80000, 'cancelled_at' => now(), 'cancellation_reason' => 'Strike']);
        $this->departure(['price_per_person' => 70000, 'departs_on' => '2027-01-10', 'returns_on' => '2027-01-16']);
        $this->departure(['price_per_person' => 60000, 'departs_on' => '2026-12-01', 'returns_on' => '2026-12-07']);
        $this->departure(['price_per_person' => 125000]);

        $this->assertSame(125000, $this->trip->fresh()->starting_from_price);
    }

    public function test_starting_price_leaves_out_full_departures_while_another_has_room(): void
    {
        $this->book($this->departure(['price_per_person' => 100000]), 8);
        $this->departure(['price_per_person' => 130000, 'departs_on' => '2027-09-01', 'returns_on' => '2027-09-07']);

        $this->assertSame(130000, $this->trip->fresh()->starting_from_price);
    }

    public function test_starting_price_shows_the_cheapest_full_departure_when_every_departure_is_full(): void
    {
        $this->book($this->departure(['price_per_person' => 100000]), 8);
        $this->book($this->departure(['price_per_person' => 130000, 'departs_on' => '2027-09-01', 'returns_on' => '2027-09-07']), 8);

        $this->assertSame(100000, $this->trip->fresh()->starting_from_price);
    }

    public function test_group_trip_without_departures_or_prices_has_no_starting_price(): void
    {
        TripPrice::factory()->for($this->trip)->create(['base_price_pp' => 50000]);

        $trip = $this->trip->fresh();

        $this->assertNull($trip->starting_from_price);
        $this->assertTrue($trip->is_expected);

        $this->departure(['price_per_person' => null, 'departs_on' => '2028-06-20', 'returns_on' => '2028-06-26']);
        $this->assertNull($this->trip->fresh()->starting_from_price);
    }

    public function test_individual_trip_keeps_the_lowest_of_its_price_rows(): void
    {
        $individual = Trip::factory()->create();
        TripPrice::factory()->for($individual)->create(['base_price_pp' => 89900]);
        TripPrice::factory()->for($individual)->create(['base_price_pp' => 109900]);

        $this->assertEquals(89900, $individual->fresh()->starting_from_price);
    }

    public function test_trip_lists_show_the_starting_price_of_a_group_trip_without_a_query_per_trip(): void
    {
        $this->seed(CountrySeeder::class);
        $this->trip->destinations()->attach(Destination::factory()->create());
        $this->departure();
        $this->book($this->departure(['price_per_person' => 110000, 'departs_on' => '2027-09-01', 'returns_on' => '2027-09-07']), 2);
        $other = Trip::factory()->withHeroImage()->hasAttached(Destination::factory())->create(['format' => TripFormat::Group, 'published_at' => '2027-01-01']);
        Departure::factory()->for($other)->create(['departs_on' => '2027-05-01', 'returns_on' => '2027-05-07', 'price_per_person' => 95000]);

        foreach (['trips.index' => 'Trip/Index', 'home' => 'Home'] as $route => $component) {
            $this->assertNoLazyLoading(fn () => $this->get(route($route))
                ->assertInertia(fn (AssertableInertia $page) => $page
                    ->component($component)
                    ->where('trips', fn ($trips) => collect($trips)->pluck('price_formatted', 'id')->sortKeys()->all() === [
                        $this->trip->id => '1.100',
                        $other->id => '950',
                    ])
                    ->where('trips.0.is_expected', false)
                    ->missing('trips.0.visible_departures')
                    ->missing('trips.0.visibleDepartures')
                )
            );
        }
    }
}
