<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tighten\Ziggy\BladeRouteGenerator;

class RouteListTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // @routes only prints the full script on its first call in a process, so every test starts from there
        BladeRouteGenerator::$generated = false;
    }

    public function test_a_public_page_leaves_the_admin_routes_out_of_its_route_list(): void
    {
        $this->get(route('about'))
            ->assertOk()
            ->assertSee('"trips.index":', false)
            ->assertDontSee('"admin.', false);
    }

    public function test_an_admin_page_gets_every_route(): void
    {
        $this->get('/admin/login')
            ->assertOk()
            ->assertSee('"trips.index":', false)
            ->assertSee('"admin.login":', false);
    }
}
