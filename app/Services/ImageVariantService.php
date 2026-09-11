<?php

namespace App\Services;

use App\Models\Image;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\Interfaces\ImageManagerInterface;
use RuntimeException;

/**
 * Generates and resolves the WebP variants of uploaded images, which are served through srcset.
 *
 * Uploads are stored as-is and are often far larger than the size they are displayed at. Each
 * image therefore gets a WebP variant per configured width, never upscaled, with its EXIF data
 * stripped. Imagick keeps the ICC colour profile while stripping; GD cannot write one, so with GD
 * browsers assume sRGB, which is the profile of every upload so far.
 */
class ImageVariantService
{
    /**
     * How much the quality is lowered per attempt when a variant exceeds the size budget.
     */
    private const QUALITY_STEP = 6;

    /**
     * Create (or overwrite) the variants of a stored image and record their dimensions.
     *
     * The dimensions of the source are recorded as well, as read after applying its EXIF orientation.
     *
     * @throws RuntimeException If the source image does not exist.
     */
    public function generate(Image $image): void
    {
        $decoded = $this->manager()->decodeBinary($this->read($image));
        $sourceWidth = $decoded->width();
        $sourceHeight = $decoded->height();
        $variants = [];

        // Widest first, so each step scales down the previous result instead of the full source
        foreach (array_reverse($this->widthsFor($sourceWidth)) as $width) {
            $encoded = $this->encode($decoded->scaleDown(width: $width));

            // Some very detailed photos exceed the budget at their widest sizes even at the minimum quality.
            // That width is skipped, so the next narrower variant is served instead.
            if (strlen($encoded) > config('images.variants.max_size') * 1024) {
                $this->disk()->delete($this->path($image->path, $width));

                continue;
            }

            $this->disk()->put($this->path($image->path, $width), $encoded);

            array_unshift($variants, [
                'width' => $decoded->width(),
                'height' => $decoded->height(),
                'size' => strlen($encoded),
            ]);
        }

        $image->update([
            'width' => $sourceWidth,
            'height' => $sourceHeight,
            'variants' => $variants,
        ]);
    }

    /**
     * Encode the fallback variant of a stored image in memory, to estimate its size without writing anything.
     *
     * @return array{source: int, variant: int} Size in bytes of the source and of its fallback variant.
     *                                          Images too narrow for any variant keep serving the source.
     *
     * @throws RuntimeException If the source image does not exist.
     */
    public function estimate(Image $image): array
    {
        $source = $this->read($image);
        $decoded = $this->manager()->decodeBinary($source);
        $width = $this->fallbackWidth($this->widthsFor($decoded->width()));

        return [
            'source' => strlen($source),
            'variant' => $width === null ? strlen($source) : strlen($this->encode($decoded->scaleDown(width: $width))),
        ];
    }

    /**
     * Whether the variants of an image have been generated and are all stored.
     */
    public function exists(Image $image): bool
    {
        return $image->variants !== null && collect($image->variants)->every(
            fn (array $variant) => $this->disk()->exists($this->path($image->path, $variant['width']))
        );
    }

    /**
     * @param  string  $path  Image path within the images directory (the Image model's `path`).
     */
    public function delete(string $path): void
    {
        $this->disk()->delete(array_map(
            fn (int $width) => $this->path($path, $width),
            config('images.variants.widths')
        ));
    }

    /**
     * Get the URL and dimensions of every variant, from narrow to wide.
     *
     * @return array<int, array{url: string, width: int, height: int}>
     */
    public function sources(Image $image): array
    {
        return array_map(fn (array $variant) => [
            'url' => url($this->disk()->url($this->path($image->path, $variant['width']))),
            'width' => $variant['width'],
            'height' => $variant['height'],
        ], $image->variants ?? []);
    }

    /**
     * Get the variant used as img src for browsers that ignore srcset, if the image has variants.
     *
     * @return array{url: string, width: int, height: int}|null
     */
    public function fallback(Image $image): ?array
    {
        $width = $this->fallbackWidth(array_column($image->variants ?? [], 'width'));

        if ($width === null) {
            return null;
        }

        return collect($this->sources($image))->firstWhere('width', $width);
    }

    /**
     * @return array<int, int> The configured widths that do not exceed the source width, from narrow to wide.
     */
    private function widthsFor(int $sourceWidth): array
    {
        $widths = array_filter(config('images.variants.widths'), fn (int $width) => $width <= $sourceWidth);
        sort($widths);

        return $widths;
    }

    /**
     * Get the configured fallback width, or the widest of the given widths below it.
     *
     * @param  array<int, int>  $widths
     */
    private function fallbackWidth(array $widths): ?int
    {
        $candidates = array_filter($widths, fn (int $width) => $width <= config('images.variants.fallback_width'));

        return $candidates === [] ? null : max($candidates);
    }

    /**
     * Encode an image as WebP at the configured quality, lowering it step by step while the result exceeds
     * the size budget. Detailed photos can otherwise end up far larger than the rest. Once the minimum
     * quality is reached, that result is returned whatever its size.
     */
    private function encode(ImageInterface $image): string
    {
        $maxBytes = config('images.variants.max_size') * 1024;
        $quality = config('images.variants.quality');

        do {
            $encoded = (string) $image->encode(new WebpEncoder(quality: $quality, strip: true));
            $quality -= self::QUALITY_STEP;
        } while (strlen($encoded) > $maxBytes && $quality >= config('images.variants.min_quality'));

        return $encoded;
    }

    /**
     * @throws RuntimeException If the source image does not exist.
     */
    private function read(Image $image): string
    {
        return $this->disk()->get($image->full_path)
            ?? throw new RuntimeException("Image not found: {$image->path}");
    }

    private function manager(): ImageManagerInterface
    {
        return ImageManager::usingDriver(extension_loaded('imagick') ? ImagickDriver::class : GdDriver::class);
    }

    /**
     * @param  string  $path  Image path within the images directory (the Image model's `path`).
     */
    private function path(string $path, int $width): string
    {
        $filename = pathinfo($path, PATHINFO_FILENAME);

        return config('images.directory').'/'.config('images.variants.directory')."/{$filename}-{$width}.webp";
    }

    private function disk(): FilesystemAdapter
    {
        /** @var FilesystemAdapter */
        return Storage::disk(config('images.disk'));
    }
}
