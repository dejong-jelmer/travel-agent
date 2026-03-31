<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TermsPdfService
{
    public const FILENAME = 'algemene-voorwaarden-omdat-we-reizen.pdf';

    private const STORAGE_PATH = 'terms/' . self::FILENAME;

    public function path(): string
    {
        if (! Storage::disk('local')->exists(self::STORAGE_PATH)) {
            $this->generate();
        }

        return Storage::disk('local')->path(self::STORAGE_PATH);
    }

    public function generate(): void
    {
        try {
            $pdf = Pdf::loadView('pdf.terms')->setPaper('a4');
            $output = $pdf->output();

            if (!Storage::disk('local')->put(self::STORAGE_PATH, $output)) {
                throw new \RuntimeException('Failed to save PDF to storage');
            }
        } catch (\Exception $e) {
            Log::error('PDF generation failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
