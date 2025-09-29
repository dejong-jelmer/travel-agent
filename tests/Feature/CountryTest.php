<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class CountryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $admin = User::factory()->create();
        $this->actingAs($admin);
    }

    public function test_admin_can_view_the_country_index_page(): void
    {
        Country::factory(10)->create();
        $response = $this->get(route('admin.countries.index'));

        $response->assertInertia(
            fn(AssertableInertia $page) => $page->component('Admin/Countries/Index')
                ->has('countries.data', 10)
                ->has('countries.links')
        );

        $response->assertStatus(200);
    }

    public function test_admin_can_view_the_country_create_page(): void
    {
        $response = $this->get(route('admin.countries.create'));

        $response->assertInertia(
            fn(AssertableInertia $page) => $page->component('Admin/Countries/Create')
        );
        $response->assertStatus(200);
    }

    public function test_admin_can_store_a_new_country(): void
    {
        $country = fake(locale_get_default())->unique()->country();
        $response = $this->post(route('admin.countries.store'), ['name' => $country]);

        $response->assertRedirect(route('admin.countries.index'));

        $this->assertDatabaseHas('countries', ['name' => $country]);
    }
}
