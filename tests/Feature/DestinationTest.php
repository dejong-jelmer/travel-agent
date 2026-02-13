<?php

namespace Tests\Feature;

use App\Enums\Destination\TravelInfo;
use App\Models\Destination;
use App\Models\Trip;
use App\Models\User;
use App\Services\CountryService;
use Database\Seeders\CountrySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class DestinationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);
        $this->seed(CountrySeeder::class);
        CountryService::resetUniquePool();
    }

    public function test_admin_can_view_destination_index(): void
    {
        Destination::factory(10)->create();

        $response = $this->get(route('admin.destinations.index'));

        $response->assertInertia(
            fn (AssertableInertia $page) => $page->component('Admin/Destination/Index')
                ->has('destinations.data', 10)
                ->has('destinations.links')
        );

        $response->assertStatus(200);
    }

    public function test_admin_can_view_destination_create(): void
    {
        $response = $this->get(route('admin.destinations.create'));

        $response->assertInertia(
            fn (AssertableInertia $page) => $page->component('Admin/Destination/Create')
        );
        $response->assertStatus(200);
    }

    public function test_admin_can_store_a_new_destination(): void
    {
        $destination = [
            'country_code' => 'nl',
            'name' => 'Netherlands',
            'region' => null,
            'travel_info' => TravelInfo::labels(),
        ];

        $response = $this->post(route('admin.destinations.store'), $destination);

        $response->assertRedirect(route('admin.destinations.index'));

        $this->assertDatabaseHas('destinations', ['country_code' => $destination['country_code'], 'name' => $destination['name']]);
    }

    public function test_admin_can_view_destination_edit(): void
    {
        $destination = Destination::factory()->create();

        $response = $this->get(route('admin.destinations.edit', $destination));

        $response->assertInertia(
            fn (AssertableInertia $page) => $page->component('Admin/Destination/Edit')
                ->has('destination')
                ->where('destination.id', $destination->id)
                ->where('destination.country_code', $destination->country_code)
                ->has('travelInfoSections')
                ->has('countries')
        );
        $response->assertStatus(200);
    }

    public function test_admin_can_update_a_destination(): void
    {
        $destination = Destination::factory()->withTravelInfo()->create();

        $travelInfo = collect(TravelInfo::cases())
            ->mapWithKeys(fn ($case) => [
                $case->value => fake()->text(fake()->numberBetween(50, 250)),
            ])->toArray();

        $updateData = [
            'country_code' => 'GB',
            'region' => 'Wales',
            'travel_info' => $travelInfo,
        ];

        $response = $this->put(route('admin.destinations.update', $destination), $updateData);

        $response->assertRedirect(route('admin.destinations.index'));
        $response->assertSessionHas('success');

        $destination->refresh();
        $this->assertEquals('GB', $destination->country_code);
        $this->assertEquals('Wales', $destination->region);
        $this->assertEquals($travelInfo, $destination->travel_info);
    }

    public function test_admin_can_destroy_a_destination_without_trips(): void
    {
        $destination = Destination::factory()->create();

        $response = $this->delete(route('admin.destinations.destroy', $destination));

        $response->assertRedirect(route('admin.destinations.index'));
        $response->assertSessionHas('success');
        $this->assertSoftDeleted('destinations', ['id' => $destination->id]);
    }

    public function test_admin_cannot_destroy_a_destination_with_trips(): void
    {
        $destination = Destination::factory()->create();
        $trip = Trip::factory()->create();
        $trip->destinations()->attach($destination->id);

        $response = $this->delete(route('admin.destinations.destroy', $destination));

        $response->assertRedirect(route('admin.destinations.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('destinations', ['id' => $destination->id]);
    }
}
