<?php

namespace App\Console\Commands;

use App\Models\Image;
use App\Services\ImageVariantService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use Throwable;

class GenerateImageVariants extends Command
{
    protected $signature = 'image-variants:generate
        {--force : Regenerate variants that already exist}
        {--dry-run : Report the number of images and the expected saving without writing anything}';

    protected $description = 'Generate the responsive WebP variants of all uploaded images';

    /**
     * Error messages of the images that could not be processed, keyed by image path.
     *
     * @var array<string, string>
     */
    private array $failures = [];

    /**
     * Generate missing variants, e.g. for images uploaded before variants existed.
     *
     * @return int Command exit code
     */
    public function handle(ImageVariantService $variants): int
    {
        $images = Image::all();

        if (! $this->option('force')) {
            $images = $images->reject(fn (Image $image) => $variants->exists($image));
        }

        if ($images->isEmpty()) {
            $this->info('All images already have variants.');

            return self::SUCCESS;
        }

        if ($this->option('dry-run')) {
            $this->estimate($images, $variants);
        } else {
            $this->generate($images, $variants);
        }

        foreach ($this->failures as $path => $error) {
            $this->warn("Skipped {$path}: {$error}");
        }

        return $this->failures === [] ? self::SUCCESS : self::FAILURE;
    }

    /**
     * @param  Collection<int, Image>  $images
     */
    private function generate(Collection $images, ImageVariantService $variants): void
    {
        $this->withProgressBar($images, function (Image $image) use ($variants) {
            try {
                $variants->generate($image);
            } catch (Throwable $e) {
                $this->failures[$image->path] = $e->getMessage();
            }
        });
        $this->newLine();

        $generated = $images->count() - count($this->failures);
        $this->info("Image variants generated: {$generated}, failed: ".count($this->failures));
    }

    /**
     * Report how many images would be processed and how many bytes their fallback variants save.
     *
     * @param  Collection<int, Image>  $images
     */
    private function estimate(Collection $images, ImageVariantService $variants): void
    {
        $sourceBytes = 0;
        $variantBytes = 0;

        $this->withProgressBar($images, function (Image $image) use ($variants, &$sourceBytes, &$variantBytes) {
            try {
                $estimate = $variants->estimate($image);
                $sourceBytes += $estimate['source'];
                $variantBytes += $estimate['variant'];
            } catch (Throwable $e) {
                $this->failures[$image->path] = $e->getMessage();
            }
        });
        $this->newLine();

        $saving = $sourceBytes - $variantBytes;
        $percentage = $sourceBytes > 0 ? round($saving / $sourceBytes * 100) : 0;

        $this->info("Dry run: variants would be generated for {$images->count()} ".Str::plural('image', $images->count()).'.');
        $this->line('Source files: '.Number::fileSize($sourceBytes, 1));
        $this->line('Fallback variants ('.config('images.variants.fallback_width').' px): '.Number::fileSize($variantBytes, 1));
        $this->line('Expected saving: '.Number::fileSize($saving, 1)." ({$percentage}%)");
    }
}
