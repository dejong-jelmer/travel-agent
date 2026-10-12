<?php

namespace App\Travel\Tests\Feature;

use App\Models\User;
use App\Travel\Database\Seeders\SampleTripSeeder;
use App\Travel\Http\PageProps;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class LibraryPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SampleTripSeeder::class);
        $this->actingAs(User::factory()->admin()->create());
    }

    public function test_it_opens_on_the_cities_with_their_pharmacies(): void
    {
        $this->get(route('admin.travel-library'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Admin/Travel/Library')
                ->where('section', 'cities')
                ->has('cities', 7)
                ->where('cities.0.name', 'Bordeaux')
                ->where('cities.0.country', 'FR')
                ->where('cities.0.pharmacies.0.name', 'Pharmacie de la Place Picard')
                ->missing('stations'));
    }

    public function test_the_stations_come_with_their_city_and_the_cities_to_pick_from(): void
    {
        $this->get(route('admin.travel-library', 'stations'))
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('section', 'stations')
                ->has('stations', 8)
                ->where('stations.0.name', 'Bordeaux Saint-Jean')
                ->where('stations.0.city', 'Bordeaux')
                ->whereType('stations.0.city_id', 'integer')
                ->has('cityOptions', 7)
                ->where('cityOptions.0.hint', 'FR'));
    }

    public function test_the_lines_come_with_their_mode_and_the_modes_to_pick_from(): void
    {
        $this->get(route('admin.travel-library', 'lines'))
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->has('transitLines', 9)
                ->where('transitLines.1', fn ($line) => $line['name'] === 'Lijn 4'
                    && $line['mode'] === 'metro'
                    && $line['color'] === '#8e2367'
                    && str_starts_with($line['info_link'], 'https://'))
                ->where('transitModeOptions.4', ['id' => 'ferry', 'name' => 'Veerboot']));
    }

    public function test_the_accommodations_come_with_their_arrival_route_and_what_it_needs(): void
    {
        $this->get(route('admin.travel-library', 'accommodations'))
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->has('accommodations', 2)
                ->where('accommodations.0.name', "Guest house L'Esprit des Chartrons")
                ->where('accommodations.0.check_in', '16:00')
                ->has('accommodations.0.arrival_route', 3)
                ->has('cityOptions', 7)
                ->has('legTypeOptions', 8)
                ->has('accommodationTypeOptions', 6)
                ->has('stations', 8)
                ->has('transitLines', 9));
    }

    public function test_an_unknown_section_is_not_found(): void
    {
        $this->get('/admin/travel-library/trips')->assertNotFound();
    }

    public function test_it_loads_each_section_without_lazy_loading(): void
    {
        foreach (PageProps::LIBRARY_SECTIONS as $section) {
            $this->assertNoLazyLoading(fn () => $this->get(route('admin.travel-library', $section))->assertOk());
        }
    }

    public function test_only_an_admin_sees_the_library(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'guest']));

        $this->get(route('admin.travel-library'))->assertForbidden();
    }
}
