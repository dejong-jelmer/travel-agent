<?php

namespace Tests\Feature;

use App\Models\Trip;
use Database\Seeders\CountrySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class TripBreadcrumbsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CountrySeeder::class);
    }

    public function test_trip_page_shares_breadcrumbs_in_order(): void
    {
        $trip = Trip::factory()->create();

        $this->get(route('trips.show', $trip))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Trip/Show')
                ->where('breadcrumbs', [
                    ['label' => __('home.title'), 'url' => route('home')],
                    ['label' => __('trip.title_index'), 'url' => route('trips.index')],
                    ['label' => $trip->name, 'url' => null],
                ])
            );
    }

    public function test_trip_page_renders_breadcrumb_list_json_ld(): void
    {
        $trip = Trip::factory()->create();

        $jsonLd = $this->jsonLd($this->get(route('trips.show', $trip)));

        $this->assertSame('https://schema.org', $jsonLd['@context']);
        $this->assertSame(['TouristTrip', 'BreadcrumbList'], array_column($jsonLd['@graph'], '@type'));

        $elements = $jsonLd['@graph'][1]['itemListElement'];

        $this->assertSame(['ListItem', 'ListItem', 'ListItem'], array_column($elements, '@type'));
        $this->assertSame([1, 2, 3], array_column($elements, 'position'));
        $this->assertSame([__('home.title'), __('trip.title_index'), $trip->name], array_column($elements, 'name'));

        // The linked items carry absolute URLs; the current page is listed without one
        $this->assertSame([route('home'), route('trips.index')], array_column($elements, 'item'));
        foreach (array_column($elements, 'item') as $url) {
            $this->assertNotFalse(filter_var($url, FILTER_VALIDATE_URL), "{$url} is not an absolute URL");
        }
        $this->assertArrayNotHasKey('item', $elements[2]);
    }

    public function test_page_without_breadcrumbs_keeps_its_single_schema(): void
    {
        $jsonLd = $this->jsonLd($this->get(route('home')));

        $this->assertSame('TravelAgency', $jsonLd['@type']);
        $this->assertArrayNotHasKey('@graph', $jsonLd);
    }

    /**
     * Decode the JSON-LD block that the seo partial renders in the head.
     *
     * @return array<string, mixed>
     */
    private function jsonLd(TestResponse $response): array
    {
        $response->assertOk();

        $found = preg_match('#<script type="application/ld\+json">\s*(.+?)\s*</script>#s', $response->getContent(), $matches);
        $this->assertSame(1, $found, 'The page renders no JSON-LD block.');

        return json_decode($matches[1], true, flags: JSON_THROW_ON_ERROR);
    }
}
