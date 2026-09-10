<?php

namespace Tests\Feature;

use App\Enums\ImageRelation;
use App\Models\BlogPost;
use App\Models\Trip;
use Database\Seeders\CountrySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Vite;
use Tests\TestCase;

class OpenGraphTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CountrySeeder::class);

        Storage::fake(config('images.disk'));
    }

    public function test_page_uses_default_og_image_with_its_dimensions(): void
    {
        [$width, $height] = getimagesize(base_path(config('seo.default_og_image')));

        $response = $this->get(route('home'));

        $response->assertSee('<meta property="og:image" content="'.$this->defaultImageUrl().'">', false);
        $response->assertSee('<meta property="og:image:type" content="image/jpeg">', false);
        $response->assertSee('<meta property="og:image:width" content="'.$width.'">', false);
        $response->assertSee('<meta property="og:image:height" content="'.$height.'">', false);
        $response->assertSee('<meta property="og:image:alt" content="'.e(__('seo.og_image_alt')).'">', false);
        $response->assertSee('<meta name="twitter:image" content="'.$this->defaultImageUrl().'">', false);
    }

    public function test_trip_uses_og_derivative_of_its_hero_image(): void
    {
        $trip = Trip::factory()->withHeroImage()->create();
        $this->storeImageFile($trip->heroImage->path);

        $this->artisan('og-images:generate')->assertSuccessful();

        $derivative = $this->derivativePath($trip->heroImage->path);
        $response = $this->get(route('trips.show', $trip));

        $response->assertSee('<meta property="og:image" content="'.url(Storage::disk(config('images.disk'))->url($derivative)).'">', false);
        $response->assertSee('<meta property="og:image:width" content="'.config('seo.og_image.width').'">', false);
        $response->assertSee('<meta property="og:image:height" content="'.config('seo.og_image.height').'">', false);
        $response->assertSee('<meta property="og:image:alt" content="'.e($trip->name).'">', false);
    }

    public function test_trip_without_og_derivative_falls_back_to_default_image(): void
    {
        $trip = Trip::factory()->withHeroImage()->create();

        $response = $this->get(route('trips.show', $trip));

        $response->assertSee('<meta property="og:image" content="'.$this->defaultImageUrl().'">', false);
    }

    public function test_hero_upload_creates_og_derivative_and_removal_deletes_it(): void
    {
        $trip = Trip::factory()->create();
        $disk = Storage::disk(config('images.disk'));

        $trip->syncImages(UploadedFile::fake()->image('hero.jpg', 1600, 1000), ImageRelation::HeroImage, true);

        $derivative = $this->derivativePath($trip->heroImage()->firstOrFail()->path);
        $disk->assertExists($derivative);
        $this->assertSame(
            [config('seo.og_image.width'), config('seo.og_image.height')],
            array_slice(getimagesizefromstring($disk->get($derivative)), 0, 2)
        );

        $trip->syncImages([], ImageRelation::HeroImage, true);

        $disk->assertMissing($derivative);
    }

    public function test_blog_post_is_shared_as_article(): void
    {
        $post = BlogPost::factory()->published()->create();

        $response = $this->get(route('blog.show', $post));

        $response->assertSee('<meta property="og:type" content="article">', false);
        $response->assertSee('<meta property="article:published_time" content="'.$post->published_at->toIso8601String().'">', false);
        $response->assertSee('<meta property="og:image" content="'.$this->defaultImageUrl().'">', false);
    }

    public function test_canonical_url_ignores_query_string_except_page_number(): void
    {
        $appUrl = rtrim(config('app.url'), '/');

        $this->get(route('trips.index', ['utm_source' => 'whatsapp']))
            ->assertSee('<link rel="canonical" href="'.$appUrl.'/reizen">', false)
            ->assertSee('<meta property="og:url" content="'.$appUrl.'/reizen">', false);

        $this->get(route('blog.index', ['page' => 2, 'utm_source' => 'whatsapp']))
            ->assertSee('<link rel="canonical" href="'.$appUrl.'/mijn-verhalen?page=2">', false);
    }

    public function test_page_with_seo_data_is_indexed(): void
    {
        $this->get(route('home'))
            ->assertSee('<meta name="robots" content="index, follow">', false);
    }

    public function test_page_without_seo_data_is_not_indexed(): void
    {
        $this->get('/admin/login')
            ->assertSee('<meta name="robots" content="noindex, follow">', false)
            ->assertDontSee('property="og:image"', false);
    }

    private function defaultImageUrl(): string
    {
        return Vite::asset(config('seo.default_og_image'));
    }

    private function derivativePath(string $path): string
    {
        return config('images.directory').'/'.config('seo.og_image.directory').'/'.pathinfo($path, PATHINFO_FILENAME).'.jpg';
    }

    private function storeImageFile(string $path): void
    {
        Storage::disk(config('images.disk'))->put(
            config('images.directory').'/'.$path,
            UploadedFile::fake()->image('hero.jpg', 1600, 1000)->getContent()
        );
    }
}
