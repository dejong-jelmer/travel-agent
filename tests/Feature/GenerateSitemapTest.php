<?php

namespace Tests\Feature;

use App\Enums\BlogPost\Status;
use App\Models\BlogPost;
use App\Models\Trip;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use Tests\TestCase;

class GenerateSitemapTest extends TestCase
{
    use RefreshDatabase;

    private TemporaryDirectory $tempDir;

    private string $path;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tempDir = (new TemporaryDirectory)->create();
        $this->path = $this->tempDir->path('sitemap.xml');
    }

    protected function tearDown(): void
    {
        $this->tempDir->delete();
        parent::tearDown();
    }

    public function test_sitemap_includes_published_content_and_excludes_drafts(): void
    {
        // Published: past date, therefore visible in the published scope
        $publishedTrip = Trip::factory()->create([
            'slug' => 'gepubliceerde-reis',
            'published_at' => today()->subDay(),
        ]);

        // Concept: future date, falls outside the published scope
        $draftTrip = Trip::factory()->create([
            'slug' => 'concept-reis',
            'published_at' => today()->addDays(30),
        ]);

        $publishedPost = BlogPost::factory()->create([
            'slug' => 'gepubliceerde-post',
            'status' => Status::Published,
            'published_at' => now()->subDay(),
        ]);

        // Concept: status Published but future date, falls outside the scope
        $draftPost = BlogPost::factory()->create([
            'slug' => 'concept-post',
            'status' => Status::Published,
            'published_at' => now()->addDays(30),
        ]);

        $this->artisan('sitemap:generate', ['--path' => $this->path])
            ->assertSuccessful();

        $xml = file_get_contents($this->path);

        // A static page belongs
        $this->assertStringContainsString(route('home'), $xml);

        // Published content belongs
        $this->assertStringContainsString(route('trips.show', $publishedTrip->slug), $xml);
        $this->assertStringContainsString(route('blog.show', $publishedPost->slug), $xml);

        // Concepts don't belong
        $this->assertStringNotContainsString(route('trips.show', $draftTrip->slug), $xml);
        $this->assertStringNotContainsString(route('blog.show', $draftPost->slug), $xml);
    }
}
