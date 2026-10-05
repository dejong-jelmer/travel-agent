<?php

namespace App\Services;

class DefaultFormPdfService extends PdfService
{
    public const FILENAME = 'standaardinformatieformulier-voor-pakketreisovereenkomsten.pdf';

    protected function storagePath(): string
    {
        return 'defaultforms/'.self::FILENAME;
    }
}
