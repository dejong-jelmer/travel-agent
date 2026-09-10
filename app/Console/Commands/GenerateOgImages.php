<?php

namespace App\Console\Commands;

use App\Models\Image;
use App\Services\OgImageService;
use Illuminate\Console\Command;
use Throwable;

class GenerateOgImages extends Command
{
    protected $signature = 'og-images:generate {--force : Regenerate derivatives that already exist}';

    protected $description = 'Generate the Open Graph derivatives of all hero images';

    /**
     * Generate missing Open Graph derivatives, e.g. for images uploaded before they existed.
     *
     * @return int Command exit code
     */
    public function handle(OgImageService $ogImages): int
    {
        $generated = 0;
        $failed = 0;

        Image::where('is_primary', true)->each(function (Image $image) use ($ogImages, &$generated, &$failed) {
            if (! $this->option('force') && $ogImages->exists($image->path)) {
                return;
            }

            try {
                $ogImages->generate($image->path);
                $generated++;
            } catch (Throwable $e) {
                $failed++;
                $this->warn("Skipped {$image->path}: {$e->getMessage()}");
            }
        });

        $this->info("Og images generated: {$generated}, failed: {$failed}");

        return $failed === 0 ? self::SUCCESS : self::FAILURE;
    }
}
