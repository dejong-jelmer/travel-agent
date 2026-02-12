<?php

namespace Tests\Feature;

use App\Enums\Destination\TravelInfo;
use App\Models\Destination;
use App\Models\User;
use App\Services\DestinationService;
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
        DestinationService::resetUniquePool();
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
}
