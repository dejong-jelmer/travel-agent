<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class SustainabilityPdfService
{
    public const FILENAME = 'reizen-met-aandacht.pdf';

    private const STORAGE_PATH = 'sustainability/'.self::FILENAME;

    public function path(): string
    {
        $disk = Storage::disk('local');

        abort_unless($disk->exists(self::STORAGE_PATH), 404);

        return $disk->path(self::STORAGE_PATH);
    }
}
