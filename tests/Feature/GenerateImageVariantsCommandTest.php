<?php

namespace Tests\Feature;

use App\Models\Image;
use App\Models\Trip;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GenerateImageVariantsCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake(config('images.disk'));
    }

    public function test_it_generates_variants_for_images_without_them(): void
    {
        $image = $this->storedImage(800, 600);

        $this->artisan('image-variants:generate')
            ->expectsOutputToContain('Image variants generated: 1, failed: 0')
            ->assertSuccessful();

        $this->assertSame([480, 768], array_column($image->fresh()->variants, 'width'));
        $this->disk()->assertExists($this->variantPath($image, 480));
        $this->disk()->assertExists($this->variantPath($image, 768));
    }

    public function test_it_skips_images_that_already_have_variants(): void
    {
        $image = $this->storedImage(800, 600);
        $this->artisan('image-variants:generate')->assertSuccessful();
        $this->disk()->put($this->variantPath($image, 480), 'existing');

        $this->artisan('image-variants:generate')
            ->expectsOutputToContain('All images already have variants.')
            ->assertSuccessful();

        $this->assertSame('existing', $this->disk()->get($this->variantPath($image, 480)));
    }

    public function test_it_regenerates_existing_variants_when_forced(): void
    {
        $image = $this->storedImage(800, 600);
        $this->artisan('image-variants:generate')->assertSuccessful();
        $this->disk()->put($this->variantPath($image, 480), 'existing');

        $this->artisan('image-variants:generate', ['--force' => true])
            ->expectsOutputToContain('Image variants generated: 1, failed: 0')
            ->assertSuccessful();

        $this->assertNotSame('existing', $this->disk()->get($this->variantPath($image, 480)));
    }

    public function test_it_completes_images_with_missing_variant_files(): void
    {
        $image = $this->storedImage(800, 600);
        $this->artisan('image-variants:generate')->assertSuccessful();
        $this->disk()->delete($this->variantPath($image, 768));

        $this->artisan('image-variants:generate')
            ->expectsOutputToContain('Image variants generated: 1, failed: 0')
            ->assertSuccessful();

        $this->disk()->assertExists($this->variantPath($image, 768));
    }

    public function test_dry_run_reports_the_expected_saving_without_writing_anything(): void
    {
        $image = $this->storedImage(1600, 1000);

        $this->artisan('image-variants:generate', ['--dry-run' => true])
            ->expectsOutputToContain('Dry run: variants would be generated for 1 image.')
            ->expectsOutputToContain('Expected saving:')
            ->assertSuccessful();

        $this->assertSame([], $this->disk()->allFiles(config('images.directory').'/'.config('images.variants.directory')));
        $this->assertNull($image->fresh()->variants);
    }

    public function test_it_reports_images_that_cannot_be_processed(): void
    {
        $missing = Trip::factory()->withImages(1)->create()->images()->sole();
        $this->storedImage(800, 600);

        $this->artisan('image-variants:generate')
            ->expectsOutputToContain('Image variants generated: 1, failed: 1')
            ->expectsOutputToContain("Skipped {$missing->path}: Image not found")
            ->assertFailed();
    }

    private function storedImage(int $width, int $height): Image
    {
        $image = Trip::factory()->withImages(1)->create()->images()->sole();
        $this->disk()->put($image->full_path, UploadedFile::fake()->image('photo.jpg', $width, $height)->getContent());

        return $image;
    }

    private function variantPath(Image $image, int $width): string
    {
        return config('images.directory').'/'.config('images.variants.directory').'/'.pathinfo($image->path, PATHINFO_FILENAME)."-{$width}.webp";
    }

    private function disk(): FilesystemAdapter
    {
        /** @var FilesystemAdapter */
        return Storage::disk(config('images.disk'));
    }
}
