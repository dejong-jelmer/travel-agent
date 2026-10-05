<?php

namespace App\Services;

class SustainabilityPdfService extends PdfService
{
    public const FILENAME = 'reizen-met-aandacht.pdf';

    protected function storagePath(): string
    {
        return 'sustainability/'.self::FILENAME;
    }
}
