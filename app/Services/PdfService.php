<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

abstract class PdfService
{
    /**
     * Path of the PDF relative to the local disk root.
     */
    abstract protected function storagePath(): string;

    public function path(): string
    {
        $disk = Storage::disk('local');

        abort_unless($disk->exists($this->storagePath()), 404);

        return $disk->path($this->storagePath());
    }
}
