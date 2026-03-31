<?php

namespace App\Console\Commands;

use App\Services\TermsPdfService;
use Illuminate\Console\Command;

class GenerateTermsPdf extends Command
{
    protected $signature = 'terms:generate-pdf';

    protected $description = 'Generate the terms and conditions PDF';

    public function handle(TermsPdfService $termsPdfService): int
    {
        try {
            $termsPdfService->generate();
            $this->info('Terms PDF generated successfully.');

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to generate PDF: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
