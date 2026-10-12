<?php

namespace Tests\Feature;

use App\Services\TermsPdfService;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TermsPdfServiceTest extends TestCase
{
    public function test_generated_pdf_embeds_only_font_subsets(): void
    {
        Storage::fake('local');
        $service = app(TermsPdfService::class);

        $service->generate();
        $pdf = file_get_contents($service->path());

        // A subset font name starts with a tag of six capital letters and a plus sign, like "SUBAAB+DejaVuSans".
        preg_match_all('#/BaseFont\s*/([^\s/]+)#', $pdf, $matches);
        $this->assertNotEmpty($matches[1], 'The PDF embeds no fonts.');
        foreach ($matches[1] as $font) {
            $this->assertMatchesRegularExpression('/^[A-Z]{6}\+/', $font, "{$font} is embedded in full, font subsetting is off.");
        }

        // The full DejaVu Sans fonts alone add about 850 kB, the subsetted terms PDF is about 90 kB.
        $this->assertLessThan(300 * 1024, strlen($pdf));
    }
}
