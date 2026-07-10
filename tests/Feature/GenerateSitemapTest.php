<?php

namespace Tests\Feature;

use App\Enums\BlogPost\Status;
use App\Models\BlogPost;
use App\Models\Trip;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenerateSitemapTest extends TestCase
{
    use RefreshDatabase;

    private string $path;

    protected function setUp(): void
    {
        parent::setUp();

        $this->path = sys_get_temp_dir().'/sitemap_test_'.uniqid().'.xml';
    }

    protected function tearDown(): void
    {
        if (file_exists($this->path)) {
            unlink($this->path);
        }

        parent::tearDown();
    }

    public function test_de_sitemap_bevat_gepubliceerde_content_en_laat_concepten_weg(): void
    {
        // Gepubliceerd: verleden datum, dus zichtbaar in de published scope
        $publishedTrip = Trip::factory()->create([
            'slug' => 'gepubliceerde-reis',
            'published_at' => today()->subDay(),
        ]);

        // Concept: toekomstige datum, valt buiten de published scope
        $draftTrip = Trip::factory()->create([
            'slug' => 'concept-reis',
            'published_at' => today()->addDays(30),
        ]);

        $publishedPost = BlogPost::factory()->create([
            'slug' => 'gepubliceerde-post',
            'status' => Status::Published,
            'published_at' => now()->subDay(),
        ]);

        // Concept: status Published maar toekomstige datum, valt buiten de scope
        $draftPost = BlogPost::factory()->create([
            'slug' => 'concept-post',
            'status' => Status::Published,
            'published_at' => now()->addDays(30),
        ]);

        $this->artisan('sitemap:generate', ['--path' => $this->path])
            ->assertSuccessful();

        $xml = file_get_contents($this->path);

        // Statische pagina hoort erin
        $this->assertStringContainsString(route('home'), $xml);

        // Gepubliceerde content hoort erin
        $this->assertStringContainsString(route('trips.show', $publishedTrip->slug), $xml);
        $this->assertStringContainsString(route('blog.show', $publishedPost->slug), $xml);

        // Concepten horen er niet in
        $this->assertStringNotContainsString(route('trips.show', $draftTrip->slug), $xml);
        $this->assertStringNotContainsString(route('blog.show', $draftPost->slug), $xml);
    }
}
