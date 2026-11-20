<?php

namespace Tests\Feature;

use App\Enums\Booking\Status as BookingStatus;
use App\Enums\SettingKey;
use App\Enums\Trip\TripFormat;
use App\Models\Booking;
use App\Models\Departure;
use App\Models\Destination;
use App\Models\Setting;
use App\Models\Trip;
use App\Models\TripPrice;
use Database\Seeders\CountrySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
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

    // Departures on the trip page

    private function showDepartures(?Trip $trip = null): array
    {
        $departures = [];

        $this->get(route('trips.show', $trip ?? $this->trip))->assertOk()->assertInertia(
            function (AssertableInertia $page) use (&$departures) {
                $departures = $page->component('Trip/Show')->toArray()['props']['departures'];
            }
        );

        return $departures;
    }

    public function test_trip_page_lists_the_departures_a_visitor_can_still_request_by_date(): void
    {
        $later = $this->departure(['departs_on' => '2027-09-01', 'returns_on' => '2027-09-07']);
        $sooner = $this->departure();
        $this->departure(['cancelled_at' => now(), 'cancellation_reason' => 'Strike']);
        $this->departure(['departs_on' => '2027-01-10', 'returns_on' => '2027-01-16']);
        $this->departure(['departs_on' => '2026-12-20', 'returns_on' => '2026-12-26']);
        Departure::factory()->create(['departs_on' => '2027-07-01', 'returns_on' => '2027-07-07']);

        $departures = $this->showDepartures();

        $this->assertSame([$sooner->id, $later->id], array_column($departures, 'id'));
        $this->assertSame('2027-06-20', $departures[0]['departs_on']);
        $this->assertSame('2027-06-26', $departures[0]['returns_on']);
        $this->assertSame(7, $departures[0]['duration_in_days']);
    }

    public function test_departure_can_be_requested_until_the_minimum_days_in_advance_of_the_trip(): void
    {
        $this->trip->update(['min_advance_days' => 21]);
        $onTheLastDay = $this->departure(['departs_on' => '2027-01-31', 'returns_on' => '2027-02-06']);
        $this->departure(['departs_on' => '2027-01-30', 'returns_on' => '2027-02-05']);

        $this->assertSame([$onTheLastDay->id], array_column($this->showDepartures(), 'id'));

        // Without a minimum the day before departure is the last day
        $this->trip->update(['min_advance_days' => null]);
        $tomorrow = $this->departure(['departs_on' => '2027-01-11', 'returns_on' => '2027-01-17']);

        $this->assertContains($tomorrow->id, array_column($this->showDepartures(), 'id'));
    }

    public function test_departure_shows_its_price_and_single_supplement_or_those_of_the_trip(): void
    {
        TripPrice::factory()->for($this->trip)->create([
            'valid_from' => '2027-01-01', 'valid_until' => '2027-12-31', 'base_price_pp' => 99000, 'single_supplement' => 20000,
        ]);
        $this->departure();
        $this->departure(['price_per_person' => null, 'single_supplement' => null, 'departs_on' => '2027-09-01', 'returns_on' => '2027-09-07']);
        $this->departure(['price_per_person' => null, 'single_supplement' => null, 'departs_on' => '2028-03-01', 'returns_on' => '2028-03-07']);

        $departures = $this->showDepartures();

        $this->assertSame([120000, 15000], [$departures[0]['price_per_person'], $departures[0]['single_supplement']]);
        $this->assertSame([99000, 20000], [$departures[1]['price_per_person'], $departures[1]['single_supplement']]);
        $this->assertSame([null, null], [$departures[2]['price_per_person'], $departures[2]['single_supplement']]);
    }

    public function test_departure_that_is_not_guaranteed_tells_its_minimum_and_decision_date(): void
    {
        $this->departure(['decision_date' => '2027-05-20']);

        $departure = $this->showDepartures()[0];

        $this->assertFalse($departure['is_guaranteed']);
        $this->assertSame(4, $departure['min_participants']);
        $this->assertSame('2027-05-20', $departure['decision_date']);
    }

    public function test_guaranteed_departure_leaves_the_minimum_and_decision_date_out(): void
    {
        $this->departure(['guaranteed_at' => now()]);

        $departure = $this->showDepartures()[0];

        $this->assertTrue($departure['is_guaranteed']);
        $this->assertNull($departure['min_participants']);
        $this->assertNull($departure['decision_date']);
    }

    public function test_decision_date_that_has_passed_is_not_promised_anymore(): void
    {
        $this->departure(['decision_date' => '2027-01-09']);
        $this->departure(['decision_date' => '2027-01-10', 'departs_on' => '2027-09-01', 'returns_on' => '2027-09-07']);

        $departures = $this->showDepartures();

        $this->assertSame(4, $departures[0]['min_participants']);
        $this->assertNull($departures[0]['decision_date']);
        $this->assertSame('2027-01-10', $departures[1]['decision_date']);
    }

    public function test_seats_left_are_only_named_when_few_are_left(): void
    {
        $roomy = $this->departure();
        $this->book($roomy, 3);
        $few = $this->departure(['departs_on' => '2027-07-01', 'returns_on' => '2027-07-07']);
        $this->book($few, 2);
        $this->book($few, 2, BookingStatus::Pending);
        $this->book($few, 3, BookingStatus::Canceled);
        $last = $this->departure(['departs_on' => '2027-08-01', 'returns_on' => '2027-08-07']);
        $this->book($last, 7, BookingStatus::New);
        $full = $this->departure(['departs_on' => '2027-09-01', 'returns_on' => '2027-09-07']);
        $this->book($full, 8);

        $departures = $this->showDepartures();

        $this->assertSame(['available', null], [$departures[0]['availability'], $departures[0]['seats_left']]);
        $this->assertSame(['few_seats', 4], [$departures[1]['availability'], $departures[1]['seats_left']]);
        $this->assertSame(['few_seats', 1], [$departures[2]['availability'], $departures[2]['seats_left']]);
        $this->assertSame(['full', null], [$departures[3]['availability'], $departures[3]['seats_left']]);
    }

    public function test_few_seats_threshold_comes_from_the_config(): void
    {
        $this->book($this->departure(), 2);
        config(['departures.few_seats_threshold' => 6]);

        $this->assertSame(6, $this->showDepartures()[0]['seats_left']);
    }

    public function test_trip_page_never_tells_how_many_people_have_booked(): void
    {
        $this->book($this->departure(), 3);

        $departure = $this->showDepartures()[0];

        foreach (['occupied_seats', 'confirmed_participants', 'max_participants', 'available_seats'] as $key) {
            $this->assertArrayNotHasKey($key, $departure);
        }
        $this->get(route('trips.show', $this->trip))->assertInertia(
            fn (AssertableInertia $page) => $page->missing('trip.visible_departures')->missing('trip.visibleDepartures')
        );
    }

    public function test_trip_page_tells_what_a_booking_pays_on_top_of_the_price(): void
    {
        Setting::set(SettingKey::BookingFee, '25.00');
        Setting::set(SettingKey::GuaranteeFund, '10.00');
        Setting::set(SettingKey::EmergencyFund, '0');
        $this->departure();

        $this->get(route('trips.show', $this->trip))->assertInertia(
            fn (AssertableInertia $page) => $page
                ->where('departureFees.total', 3500)
                ->where('departureFees.items', ['booking_fee' => 2500, 'guarantee_fund' => 1000])
        );
    }

    public function test_individual_trip_has_no_departures_on_its_page(): void
    {
        $individual = Trip::factory()->withHeroImage()->create(['published_at' => '2027-01-01']);

        $this->assertSame([], $this->showDepartures($individual));
    }

    public function test_group_trip_without_departures_still_shows_its_page(): void
    {
        $this->assertSame([], $this->showDepartures());
    }

    // Structured data

    private function touristTrip(?Trip $trip = null): array
    {
        $response = $this->get(route('trips.show', $trip ?? $this->trip))->assertOk();
        $jsonLd = [];

        $response->assertInertia(function (AssertableInertia $page) use (&$jsonLd) {
            $jsonLd = $page->toArray()['props']['seo']['jsonLd'];
        });

        return collect($jsonLd['@graph'])->firstWhere('@type', 'TouristTrip');
    }

    public function test_structured_data_of_a_group_trip_has_an_offer_per_departure(): void
    {
        $this->trip->update(['min_advance_days' => 14]);
        $this->departure(['price_per_person' => 129550]);
        $few = $this->departure(['price_per_person' => 119500, 'departs_on' => '2027-07-01', 'returns_on' => '2027-07-07']);
        $this->book($few, 5);
        $full = $this->departure(['price_per_person' => 110000, 'departs_on' => '2027-08-01', 'returns_on' => '2027-08-07']);
        $this->book($full, 8);

        $offers = $this->touristTrip()['offers'];

        $this->assertCount(3, $offers);
        $this->assertSame([
            '@type' => 'Offer',
            'price' => 1295.5,
            'priceCurrency' => 'EUR',
            'availability' => 'https://schema.org/InStock',
            'validThrough' => '2027-06-06',
            'url' => route('trips.show', $this->trip),
            'itemOffered' => [
                '@type' => 'Trip',
                'name' => $this->trip->name,
                'departureTime' => '2027-06-20',
                'arrivalTime' => '2027-06-26',
            ],
        ], $offers[0]);
        $this->assertSame('https://schema.org/LimitedAvailability', $offers[1]['availability']);
        $this->assertSame('https://schema.org/SoldOut', $offers[2]['availability']);
    }

    public function test_structured_data_leaves_out_departures_a_visitor_cannot_request_or_without_a_price(): void
    {
        $this->departure(['cancelled_at' => now(), 'cancellation_reason' => 'Strike']);
        $this->departure(['departs_on' => '2027-01-10', 'returns_on' => '2027-01-16']);
        $this->departure(['price_per_person' => null, 'departs_on' => '2028-06-20', 'returns_on' => '2028-06-26']);
        $priced = $this->departure(['departs_on' => '2027-09-01', 'returns_on' => '2027-09-07']);

        $offers = $this->touristTrip()['offers'];

        $this->assertSame(['2027-09-01'], array_column(array_column($offers, 'itemOffered'), 'departureTime'));
        $this->assertNotNull($priced);
    }

    public function test_structured_data_of_a_group_trip_without_departures_has_no_offers(): void
    {
        TripPrice::factory()->for($this->trip)->create(['base_price_pp' => 50000]);

        $this->assertArrayNotHasKey('offers', $this->touristTrip());
    }

    public function test_structured_data_of_an_individual_trip_keeps_one_aggregate_offer(): void
    {
        $individual = Trip::factory()->withHeroImage()->create(['published_at' => '2027-01-01']);
        TripPrice::factory()->for($individual)->create(['base_price_pp' => 89900]);

        $offers = $this->touristTrip($individual)['offers'];

        $this->assertSame('AggregateOffer', $offers['@type']);
        $this->assertEquals(899, $offers['lowPrice']);
    }

    public function test_trip_page_prices_its_departures_without_a_query_per_departure(): void
    {
        TripPrice::factory()->for($this->trip)->create(['valid_from' => '2027-01-01', 'valid_until' => '2027-12-31', 'base_price_pp' => 99000]);
        foreach (range(3, 8) as $month) {
            $this->departure(['price_per_person' => null, 'single_supplement' => null, 'departs_on' => "2027-0{$month}-10", 'returns_on' => "2027-0{$month}-16"]);
        }

        DB::enableQueryLog();
        $departures = $this->showDepartures();
        $priceQueries = collect(DB::getQueryLog())->filter(fn (array $query) => str_contains($query['query'], 'from `trip_prices`'));
        DB::disableQueryLog();

        $this->assertCount(6, $departures);
        $this->assertSame([99000], array_values(array_unique(array_column($departures, 'price_per_person'))));
        $this->assertCount(1, $priceQueries);
    }
}
