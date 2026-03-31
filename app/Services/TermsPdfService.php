<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class TermsPdfService
{
    public const FILENAME = 'algemene-voorwaarden-omdat-we-reizen.pdf';

    private const STORAGE_PATH = 'terms/'.self::FILENAME;

    public function path(): string
    {
        if (! Storage::disk('local')->exists(self::STORAGE_PATH)) {
            $this->generate();
        }

        return Storage::disk('local')->path(self::STORAGE_PATH);
    }

    public function generate(): void
    {
        $pdf = Pdf::loadView('pdf.terms')->setPaper('a4');

        Storage::disk('local')->put(self::STORAGE_PATH, $pdf->output());
    }
}
