<?php

namespace Tests\Feature;

use App\Enums\Trip\ItineraryType;
use App\Models\Destination;
use App\Models\Itinerary;
use App\Models\Trip;
use App\Models\TripPrice;
use Database\Seeders\CountrySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class TripsPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CountrySeeder::class);
    }

    public function test_trips_page_renders_correct_inertia_component(): void
    {
        $this->get(route('trips.index'))
            ->assertInertia(fn (AssertableInertia $page) => $page->component('Trip/Index'))
            ->assertStatus(200);
    }

    public function test_trips_page_only_shows_published_trips(): void
    {
        $published = Trip::factory()->create();
        Trip::factory()->create(['published_at' => now()->addDay()]);

        $this->get(route('trips.index'))
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Trip/Index')
                ->has('trips', 1)
                ->where('trips.0.id', $published->id)
            );
    }

    public function test_trips_page_passes_the_travel_mode_of_each_trip(): void
    {
        $trip = Trip::factory()->create();
        Itinerary::factory()->for($trip)->create(['type' => ItineraryType::NightTrain]);

        $this->get(route('trips.index'))
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Trip/Index')
                ->has('trips', 1)
                ->where('trips.0.travel_mode', ['value' => 'night_train', 'label' => 'Nachttrein'])
            );
    }

    public function test_trips_page_loads_the_trips_without_a_query_per_trip(): void
    {
        $trips = Trip::factory()->count(3)->withHeroImage()->withImages(2)
            ->has(TripPrice::factory()->count(2), 'prices')
            ->hasAttached(Destination::factory())
            ->create();
        Itinerary::factory()->for($trips->first())->create(['type' => ItineraryType::NightTrain]);

        $this->assertNoLazyLoading(fn () => $this->get(route('trips.index'))
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Trip/Index')
                ->has('trips', 3)
                ->whereType('trips.0.price_formatted', 'string')
                ->whereType('trips.0.destinations_formatted', 'string')
                ->missing('trips.0.image_paths')
                ->has('countries', 3)
            )
        );
    }

    public function test_countries_prop_is_a_flat_array_with_expected_keys(): void
    {
        $destination = Destination::factory()->create();
        $trip = Trip::factory()->create();
        $trip->destinations()->attach($destination);

        $this->get(route('trips.index'))
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Trip/Index')
                ->whereType('countries', 'array')
                ->has('countries', 1)
                ->where('countries.0.code', $destination->country_code)
                ->has('countries.0.name')
                ->has('countries.0.en_name')
            );
    }

    public function test_countries_prop_is_not_wrapped_in_data_key(): void
    {
        $destination = Destination::factory()->create();
        $trip = Trip::factory()->create();
        $trip->destinations()->attach($destination);

        $this->get(route('trips.index'))
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Trip/Index')
                ->missing('countries.data')
            );
    }

    public function test_countries_are_deduplicated_when_multiple_trips_share_a_destination(): void
    {
        $destination = Destination::factory()->create();

        $trip1 = Trip::factory()->create();
        $trip1->destinations()->attach($destination);

        $trip2 = Trip::factory()->create();
        $trip2->destinations()->attach($destination);

        $this->get(route('trips.index'))
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Trip/Index')
                ->has('countries', 1)
            );
    }

    public function test_unpublished_trips_do_not_contribute_to_countries(): void
    {
        $destination = Destination::factory()->create();
        $draft = Trip::factory()->create(['published_at' => now()->addDay()]);
        $draft->destinations()->attach($destination);

        $this->get(route('trips.index'))
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Trip/Index')
                ->has('countries', 0)
            );
    }
}
