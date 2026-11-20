<?php

namespace Tests\Feature;

use App\Enums\BlogPost\Status;
use App\Enums\Trip\TripFormat;
use App\Models\BlogPost;
use App\Models\Departure;
use App\Models\Trip;
use App\Services\SitemapBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use Tests\TestCase;

class SitemapTest extends TestCase
{
    use RefreshDatabase;

    private Trip $publishedTrip;

    private Trip $draftTrip;

    private BlogPost $publishedPost;

    private BlogPost $draftPost;

    protected function setUp(): void
    {
        parent::setUp();

        // Published: past date, therefore visible in the published scope
        $this->publishedTrip = Trip::factory()->create([
            'slug' => 'gepubliceerde-reis',
            'published_at' => today()->subDay(),
        ]);

        // Concept: future date, falls outside the published scope
        $this->draftTrip = Trip::factory()->create([
            'slug' => 'concept-reis',
            'published_at' => today()->addDays(30),
        ]);

        $this->publishedPost = BlogPost::factory()->create([
            'slug' => 'gepubliceerde-post',
            'status' => Status::Published,
            'published_at' => now()->subDay(),
        ]);

        // Concept: status Published but future date, falls outside the scope
        $this->draftPost = BlogPost::factory()->create([
            'slug' => 'concept-post',
            'status' => Status::Published,
            'published_at' => now()->addDays(30),
        ]);
    }

    public function test_sitemap_route_returns_xml(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $this->assertStringContainsString('application/xml', (string) $response->headers->get('Content-Type'));
        $this->assertStringContainsString('<urlset', $response->getContent());
    }

    public function test_sitemap_route_includes_every_static_page(): void
    {
        $xml = $this->get('/sitemap.xml')->getContent();

        foreach (SitemapBuilder::STATIC_ROUTES as $name) {
            $this->assertStringContainsString(route($name), $xml, "Route '{$name}' is missing from the sitemap");
        }
    }

    public function test_sitemap_route_excludes_noindex_pages(): void
    {
        $xml = $this->get('/sitemap.xml')->getContent();

        // Both pages are served with a noindex header, so listing them here
        // would contradict their own robots meta tag
        $this->assertStringNotContainsString(route('privacy'), $xml);
        $this->assertStringNotContainsString(route('terms'), $xml);
    }

    public function test_sitemap_route_includes_published_content_and_excludes_drafts(): void
    {
        $xml = $this->get('/sitemap.xml')->getContent();

        $this->assertStringContainsString(route('trips.show', $this->publishedTrip->slug), $xml);
        $this->assertStringContainsString(route('blog.show', $this->publishedPost->slug), $xml);

        $this->assertStringNotContainsString(route('trips.show', $this->draftTrip->slug), $xml);
        $this->assertStringNotContainsString(route('blog.show', $this->draftPost->slug), $xml);
    }

    public function test_trip_is_last_modified_when_it_or_one_of_its_departures_last_changed(): void
    {
        $trip = $this->lastModifiedTrip();
        $this->travelTo('2027-03-01 10:00:00', fn () => $trip->touch());

        $this->assertSame('2027-03-01', $this->sitemapLastModified(route('trips.show', $trip->slug)));

        $departure = $this->travelTo('2027-04-15 08:00:00', fn () => Departure::factory()->for($trip)->create([
            'departs_on' => '2027-09-01',
            'returns_on' => '2027-09-07',
        ]));
        $this->assertSame('2027-04-15', $this->sitemapLastModified(route('trips.show', $trip->slug)));

        // A departure that changed before the trip itself leaves the trip's own date
        $this->travelTo('2027-05-01 08:00:00', fn () => $trip->touch());
        $this->assertSame('2027-05-01', $this->sitemapLastModified(route('trips.show', $trip->slug)));

        $this->travelTo('2027-05-20 08:00:00', fn () => $departure->update(['cancelled_at' => now(), 'cancellation_reason' => 'Strike']));
        $this->assertSame('2027-05-20', $this->sitemapLastModified(route('trips.show', $trip->slug)));
    }

    public function test_story_is_last_modified_when_it_was_last_saved(): void
    {
        $post = $this->travelTo('2027-03-01 10:00:00', fn () => BlogPost::factory()->create([
            'slug' => 'bijgewerkt-verhaal',
            'status' => Status::Published,
            'published_at' => '2027-02-20 09:00:00',
        ]));
        $this->travelTo('2027-03-02 10:00:00', fn () => $this->assertSame(
            '2027-03-01',
            $this->sitemapLastModified(route('blog.show', $post->slug)),
        ));

        $this->travelTo('2027-04-15 08:00:00', fn () => $post->update(['title' => 'Een nieuwe titel']));
        $this->travelTo('2027-04-16 08:00:00', fn () => $this->assertSame(
            '2027-04-15',
            $this->sitemapLastModified(route('blog.show', $post->slug)),
        ));
    }

    public function test_story_scheduled_ahead_is_last_modified_when_it_went_live(): void
    {
        // Saved on 1 March, published by its date on 10 March without another save
        $post = $this->travelTo('2027-03-01 10:00:00', fn () => BlogPost::factory()->create([
            'slug' => 'gepland-verhaal',
            'status' => Status::Published,
            'published_at' => '2027-03-10 07:00:00',
        ]));

        $this->travelTo('2027-03-12 10:00:00', fn () => $this->assertSame(
            '2027-03-10',
            $this->sitemapLastModified(route('blog.show', $post->slug)),
        ));
    }

    private function lastModifiedTrip(): Trip
    {
        return Trip::factory()->create([
            'slug' => 'groepsreis',
            'format' => TripFormat::Group,
            'published_at' => today()->subDay(),
        ]);
    }

    /**
     * The date part of the lastmod the sitemap gives a page.
     */
    private function sitemapLastModified(string $url): string
    {
        // Built directly: the route caches the XML, which would hide the change from a second look
        $xml = simplexml_load_string(app(SitemapBuilder::class)->build()->render());
        $xml->registerXPathNamespace('s', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $lastmod = $xml->xpath("//s:url[s:loc='{$url}']/s:lastmod");

        return substr((string) $lastmod[0], 0, 10);
    }

    public function test_sitemap_command_writes_the_same_sitemap_to_a_file(): void
    {
        $tempDir = (new TemporaryDirectory)->create();
        $path = $tempDir->path('sitemap.xml');

        $this->artisan('sitemap:generate', ['--path' => $path])
            ->assertSuccessful();

        $xml = file_get_contents($path);

        $this->assertStringContainsString(route('home'), $xml);
        $this->assertStringContainsString(route('trips.show', $this->publishedTrip->slug), $xml);
        $this->assertStringNotContainsString(route('trips.show', $this->draftTrip->slug), $xml);

        $tempDir->delete();
    }
}
