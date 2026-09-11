<?php

namespace Tests\Feature;

use App\Enums\ImageRelation;
use App\Jobs\CreateImageVariants;
use App\Models\Image;
use App\Models\Trip;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CreateImageVariantsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake(config('images.disk'));
    }

    public function test_it_creates_a_webp_variant_per_configured_width(): void
    {
        $image = $this->storedImage(UploadedFile::fake()->image('photo.jpg', 1600, 1000)->getContent());

        CreateImageVariants::dispatch($image);

        $image->refresh();
        $this->assertSame([1600, 1000], [$image->width, $image->height]);
        $this->assertSame(
            [[480, 300], [768, 480], [1200, 750], [1600, 1000]],
            array_map(fn (array $variant) => [$variant['width'], $variant['height']], $image->variants)
        );

        foreach ($image->variants as $variant) {
            $file = $this->disk()->get($this->variantPath($image, $variant['width']));
            $info = getimagesizefromstring($file);

            $this->assertSame([$variant['width'], $variant['height']], array_slice($info, 0, 2));
            $this->assertSame('image/webp', $info['mime']);
            $this->assertSame(strlen($file), $variant['size']);
        }
    }

    public function test_it_never_upscales(): void
    {
        $image = $this->storedImage(UploadedFile::fake()->image('photo.jpg', 1000, 625)->getContent());

        CreateImageVariants::dispatch($image);

        $this->assertSame([480, 768], array_column($image->fresh()->variants, 'width'));
        $this->disk()->assertMissing($this->variantPath($image, 1200));
        $this->disk()->assertMissing($this->variantPath($image, 1600));
    }

    public function test_it_encodes_with_the_configured_quality(): void
    {
        $image = $this->storedImage($this->noisyJpeg(600, 400));

        config(['images.variants.quality' => 90]);
        CreateImageVariants::dispatch($image);
        $highQualitySize = $image->fresh()->variants[0]['size'];

        config(['images.variants.quality' => 30]);
        CreateImageVariants::dispatch($image);
        $lowQualitySize = $image->fresh()->variants[0]['size'];

        $this->assertLessThan($highQualitySize, $lowQualitySize);
    }

    public function test_it_lowers_the_quality_of_variants_over_the_size_budget(): void
    {
        $image = $this->storedImage($this->noisyJpeg(600, 400));
        CreateImageVariants::dispatch($image);
        $sizeAtConfiguredQuality = $image->fresh()->variants[0]['size'];

        $budget = intdiv($sizeAtConfiguredQuality, 1024) - 1;
        config(['images.variants.max_size' => $budget]);
        CreateImageVariants::dispatch($image);

        $this->assertLessThanOrEqual($budget * 1024, $image->fresh()->variants[0]['size']);
    }

    public function test_it_skips_variants_that_exceed_the_budget_at_the_minimum_quality(): void
    {
        $image = $this->storedImage($this->noisyJpeg(600, 400));
        CreateImageVariants::dispatch($image);
        $this->disk()->assertExists($this->variantPath($image, 480));

        config(['images.variants.max_size' => 1]);
        CreateImageVariants::dispatch($image);

        $this->assertSame([], $image->fresh()->variants);
        $this->disk()->assertMissing($this->variantPath($image, 480));
    }

    public function test_it_applies_the_exif_orientation_and_strips_the_exif_data(): void
    {
        // A landscape photo whose EXIF data says to turn it a quarter clockwise (orientation 6)
        $jpeg = $this->jpegWithOrientation(1000, 600, 6);
        $this->assertSame(6, exif_read_data('data://image/jpeg;base64,'.base64_encode($jpeg))['Orientation']);
        $image = $this->storedImage($jpeg);

        CreateImageVariants::dispatch($image);

        $image->refresh();
        $this->assertSame([600, 1000], [$image->width, $image->height]);
        $this->assertSame([[480, 800]], array_map(fn (array $variant) => [$variant['width'], $variant['height']], $image->variants));
        $this->assertStringNotContainsString('EXIF', $this->disk()->get($this->variantPath($image, 480)));
    }

    public function test_uploading_an_image_queues_its_variants(): void
    {
        Queue::fake();
        $trip = Trip::factory()->create();

        $trip->syncImages(UploadedFile::fake()->image('photo.jpg', 800, 600), ImageRelation::Images);

        $image = $trip->images()->sole();
        Queue::assertPushed(CreateImageVariants::class, fn (CreateImageVariants $job) => $job->image->is($image));
    }

    public function test_removing_an_image_deletes_its_variants(): void
    {
        $trip = Trip::factory()->create();
        $trip->syncImages(UploadedFile::fake()->image('photo.jpg', 800, 600), ImageRelation::Images);
        $image = $trip->images()->sole();
        $this->disk()->assertExists($this->variantPath($image, 480));
        $this->disk()->assertExists($this->variantPath($image, 768));

        $trip->syncImages([], ImageRelation::Images);

        $this->disk()->assertMissing($this->variantPath($image, 480));
        $this->disk()->assertMissing($this->variantPath($image, 768));
    }

    private function storedImage(string $contents): Image
    {
        $image = Trip::factory()->withImages(1)->create()->images()->sole();
        $this->disk()->put($image->full_path, $contents);

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

    /**
     * Create a JPEG whose EXIF data holds the given orientation.
     */
    private function jpegWithOrientation(int $width, int $height, int $orientation): string
    {
        $jpeg = UploadedFile::fake()->image('photo.jpg', $width, $height)->getContent();

        // Little-endian TIFF structure with a single IFD0 entry: Orientation (0x0112), type SHORT, count 1
        $tiff = 'II'.pack('vV', 0x2A, 8).pack('v', 1).pack('vvVvv', 0x0112, 3, 1, $orientation, 0).pack('V', 0);
        $exif = "Exif\x00\x00".$tiff;
        $app1 = "\xFF\xE1".pack('n', strlen($exif) + 2).$exif;

        // Insert the APP1 segment after the SOI marker and the JFIF APP0 segment
        $offset = 4 + unpack('n', substr($jpeg, 4, 2))[1];

        return substr($jpeg, 0, $offset).$app1.substr($jpeg, $offset);
    }

    /**
     * Create a JPEG of random blocks, which unlike a plain fake image compresses differently per quality.
     */
    private function noisyJpeg(int $width, int $height): string
    {
        $gd = imagecreatetruecolor($width, $height);

        for ($y = 0; $y < $height; $y += 4) {
            for ($x = 0; $x < $width; $x += 4) {
                imagefilledrectangle($gd, $x, $y, $x + 3, $y + 3, random_int(0, 0xFFFFFF));
            }
        }

        ob_start();
        imagejpeg($gd, null, 90);

        return (string) ob_get_clean();
    }
}
