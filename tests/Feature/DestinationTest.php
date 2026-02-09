<?php

namespace Tests\Feature;

use App\Models\Destination;
use App\Models\User;
use App\Services\DestinationService;
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
        $destination = fake(locale_get_default())->unique()->destination();
        $response = $this->post(route('admin.destinations.store'), ['name' => $destination]);

        $response->assertRedirect(route('admin.destinations.index'));

        $this->assertDatabaseHas('destinations', ['name' => $destination]);
    }
}
