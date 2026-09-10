<?php

namespace Tests\Feature;

use App\Enums\BlogPost\Status;
use App\Models\BlogPost;
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
