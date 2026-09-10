<?php

namespace App\Services;

use App\DTO\OgImageData;
use App\Models\Image;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Vite;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\ImageManager;
use RuntimeException;

/**
 * Resolves and generates the Open Graph images used in link previews.
 *
 * Hero uploads are stored as-is and can be WebP or several megabytes, which WhatsApp
 * and LinkedIn do not reliably render. Each hero image therefore gets a JPEG derivative
 * cropped to the recommended 1.91:1 size; without one, the default image is used.
 */
class OgImageService
{
    private const MIME_TYPE = 'image/jpeg';

    /**
     * Get the Open Graph image for an uploaded image, falling back to the default.
     *
     * @param  string  $alt  Description of the uploaded image, used when its derivative exists.
     */
    public function forImage(?Image $image, string $alt): OgImageData
    {
        if ($image === null || ! $this->exists($image->path)) {
            return $this->default();
        }

        return new OgImageData(
            url: url($this->disk()->url($this->derivativePath($image->path))),
            width: config('seo.og_image.width'),
            height: config('seo.og_image.height'),
            type: self::MIME_TYPE,
            alt: $alt,
        );
    }

    /**
     * Get the site-wide default Open Graph image.
     *
     * Served through Vite so that its URL carries a content hash: replacing the file
     * changes the URL, which makes platforms fetch the new image.
     */
    public function default(): OgImageData
    {
        $path = config('seo.default_og_image');
        $size = getimagesize(base_path($path)) ?: throw new RuntimeException("Default og image not found: {$path}");

        return new OgImageData(
            url: Vite::asset($path),
            width: $size[0],
            height: $size[1],
            type: $size['mime'],
            alt: __('seo.og_image_alt'),
        );
    }

    /**
     * Create (or overwrite) the Open Graph derivative of a stored image.
     *
     * @param  string  $path  Image path within the images directory (the Image model's `path`).
     *
     * @throws RuntimeException If the source image does not exist.
     */
    public function generate(string $path): void
    {
        $source = $this->disk()->get(config('images.directory')."/{$path}")
            ?? throw new RuntimeException("Image not found: {$path}");

        $encoded = ImageManager::usingDriver(Driver::class)
            ->decodeBinary($source)
            ->cover(config('seo.og_image.width'), config('seo.og_image.height'))
            ->encode(new JpegEncoder(quality: config('seo.og_image.quality'), progressive: true, strip: true));

        $this->disk()->put($this->derivativePath($path), (string) $encoded);
    }

    /**
     * @param  string  $path  Image path within the images directory (the Image model's `path`).
     */
    public function exists(string $path): bool
    {
        return $this->disk()->exists($this->derivativePath($path));
    }

    /**
     * @param  string  $path  Image path within the images directory (the Image model's `path`).
     */
    public function delete(string $path): void
    {
        $this->disk()->delete($this->derivativePath($path));
    }

    private function derivativePath(string $path): string
    {
        $filename = pathinfo($path, PATHINFO_FILENAME);

        return config('images.directory').'/'.config('seo.og_image.directory')."/{$filename}.jpg";
    }

    private function disk(): FilesystemAdapter
    {
        /** @var FilesystemAdapter */
        return Storage::disk(config('images.disk'));
    }
}
