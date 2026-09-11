<?php

namespace App\Jobs;

use App\Models\Image;
use App\Services\ImageVariantService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CreateImageVariants implements ShouldQueue
{
    use Queueable;

    /**
     * Determine number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds the job can run before timing out, kept below the queue's retry_after.
     */
    public int $timeout = 60;

    /**
     * Discard the job when the image was removed before it ran.
     */
    public bool $deleteWhenMissingModels = true;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Image $image,
    ) {}

    /**
     * Generate the WebP variants of the image.
     */
    public function handle(ImageVariantService $variants): void
    {
        $variants->generate($this->image);
    }
}
