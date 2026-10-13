<?php

namespace Tests\Feature;

use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class ServerSideRenderingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // phpunit.xml switches SSR off; these tests switch it on, with a fake SSR server instead of `inertia:start-ssr`
        config(['inertia.ssr.enabled' => true, 'inertia.ssr.ensure_bundle_exists' => false]);
        Http::fake(['*/render' => Http::response(['head' => [], 'body' => '<div id="app">Rendered on the server</div>'])]);
    }

    public function test_a_public_page_is_rendered_on_the_ssr_server_with_the_route_list(): void
    {
        $this->get(route('about'))
            ->assertOk()
            ->assertSee('Rendered on the server');

        Http::assertSent(fn (ClientRequest $request) => isset($request['props']['ziggy']['routes']['home'])
            && $request['props']['ziggy']['location'] === route('about'));
    }

    public function test_an_admin_page_is_rendered_in_the_browser_only(): void
    {
        $this->get('/admin/login')
            ->assertOk()
            ->assertDontSee('Rendered on the server')
            ->assertInertia(fn (AssertableInertia $page) => $page->where('ziggy', null));

        Http::assertNothingSent();
    }

    public function test_an_inertia_visit_leaves_the_route_list_out(): void
    {
        $version = (new HandleInertiaRequests)->version(new Request);

        $this->get(route('about'), ['X-Inertia' => 'true', 'X-Inertia-Version' => (string) $version])
            ->assertOk()
            ->assertHeader('X-Inertia')
            ->assertJsonPath('props.ziggy', null);
    }
}
