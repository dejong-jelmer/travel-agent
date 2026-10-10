<?php

namespace Tests\Feature;

use App\Models\Trip;
use Database\Seeders\CountrySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HeroPreloadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CountrySeeder::class);

        Storage::fake(config('images.disk'));
    }

    public function test_trip_page_preloads_its_hero_image_with_the_srcset_and_sizes_of_the_hero(): void
    {
        $trip = Trip::factory()->withHeroImage()->create();
        $trip->heroImage->update(['variants' => array_map(
            fn (int $width) => ['width' => $width, 'height' => (int) ($width * 2 / 3), 'size' => 1000],
            [480, 768, 1200, 1600]
        )]);

        $response = $this->get(route('trips.show', $trip));

        $srcset = implode(', ', array_map(
            fn (int $width) => $this->variantUrl($trip->heroImage->path, $width)." {$width}w",
            [480, 768, 1200, 1600]
        ));
        $response->assertOk();
        $this->assertMatchesRegularExpression(
            '/<link rel="preload" as="image" href="'.preg_quote(e($this->variantUrl($trip->heroImage->path, 1200)), '/')
                .'"\s+imagesrcset="'.preg_quote(e($srcset), '/').'" imagesizes="100vw"\s+fetchpriority="high">/',
            $response->getContent()
        );
    }

    public function test_trip_page_preloads_the_original_upload_of_a_hero_image_without_variants(): void
    {
        $trip = Trip::factory()->withHeroImage()->create();

        $response = $this->get(route('trips.show', $trip));

        $response->assertOk();
        $this->assertMatchesRegularExpression(
            '/<link rel="preload" as="image" href="'.preg_quote(e($trip->heroImage->public_url), '/').'"\s+fetchpriority="high">/',
            $response->getContent()
        );
        $response->assertDontSee('imagesrcset', false);
    }

    // The poster preload of the home page is covered by HomeTest::test_hero_poster_is_preloaded_only_on_home_page
    public function test_page_without_hero_image_preloads_no_image(): void
    {
        $response = $this->get(route('trips.index'));

        $response->assertOk();
        $response->assertDontSee('<link rel="preload" as="image"', false);
    }

    private function variantUrl(string $path, int $width): string
    {
        $filename = pathinfo($path, PATHINFO_FILENAME);

        return url(Storage::disk(config('images.disk'))->url("images/variants/{$filename}-{$width}.webp"));
    }
}
