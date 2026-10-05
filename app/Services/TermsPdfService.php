<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TermsPdfService extends PdfService
{
    public const FILENAME = 'algemene-voorwaarden-omdat-we-reizen.pdf';

    private const STORAGE_PATH = 'terms/'.self::FILENAME;

    protected function storagePath(): string
    {
        return self::STORAGE_PATH;
    }

    public function path(): string
    {
        $disk = Storage::disk('local');

        // Add locking to prevent race conditions
        if (! $disk->exists(self::STORAGE_PATH)) {
            Cache::lock('terms-pdf-generation', 10)->block(15, function () use ($disk) {
                if (! $disk->exists(self::STORAGE_PATH)) { // @phpstan-ignore booleanNot.alwaysTrue
                    $this->generate();
                }
            });
        }

        return parent::path();
    }

    public function generate(): void
    {
        $data = [
            'companyName' => config('app.name'),
            'kvk' => config('contact.kvk'),
            'version' => config('terms.version'),
            'updated' => config('terms.updated'),
        ];

        try {
            $pdf = Pdf::loadView('pdf.terms', $data)->setPaper('a4');
            $output = $pdf->output();

            Storage::disk('local')->put(self::STORAGE_PATH, $output);
        } catch (\Exception $e) {
            Log::error('PDF generation failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
