<?php

namespace App\Travel\Documents;

use App\Models\Booking;
use App\Travel\Contracts\BookingDetails;
use App\Travel\Data\TravelDocumentData;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdf;
use Illuminate\Support\Facades\File;

/**
 * The travel documents of a booking, in three parts: the schedule (Reisschema), the stays (Verblijf) and the
 * practical information (Praktisch). The PDF holds all three; the page behind the share link shows one at a time.
 */
class TravelDocuments
{
    /**
     * The parts, by the name in their url and anchor, with their tab label.
     */
    public const PARTS = [
        'reisschema' => 'Reisschema',
        'verblijf' => 'Verblijf',
        'praktisch' => 'Praktisch',
    ];

    /**
     * A PDF page is a phone screen, as the design: 430 by 932 pixels. dompdf counts 96 pixels to the inch, so the
     * pixels of the design carry over, and a page is 0.75 points to the pixel.
     */
    private const PAGE = [430, 932];

    /**
     * dompdf makes a line its line-height times the height of the font, ascender to descender, which for Manrope is
     * 1.366 em, times this ratio. At the inverse a line is as high as in a browser; at dompdf's 1.1 it is half again.
     */
    private const FONT_HEIGHT_RATIO = 1 / 1.366;

    public function __construct(private readonly BookingDetails $details) {}

    public function for(Booking $booking): TravelDocumentData
    {
        return TravelDocumentData::fromBooking(
            $booking,
            $this->details->title($booking),
            $this->details->travelerNames($booking),
            $this->details->emergencyPhone($booking),
        );
    }

    /**
     * With font subsetting, which laravel-dompdf leaves off, the PDF holds only the glyphs it uses of Manrope. dompdf
     * keeps what it learns of the font in a cache, which goes in the framework's cache folder, as storage/fonts is
     * neither there nor ignored by git.
     */
    public function pdf(TravelDocumentData $document): DomPdf
    {
        $fontCache = storage_path('framework/cache/dompdf-fonts');
        File::ensureDirectoryExists($fontCache);

        return Pdf::setOption([
            'fontDir' => $fontCache,
            'fontCache' => $fontCache,
            'isFontSubsettingEnabled' => true,
            'fontHeightRatio' => self::FONT_HEIGHT_RATIO,
        ])
            ->loadView('travel::documents.pdf', ['document' => $document, 'fonts' => self::fontPath()])
            ->setPaper([0, 0, self::PAGE[0] * 0.75, self::PAGE[1] * 0.75]);
    }

    public function filename(Booking $booking): string
    {
        return "reisdocumenten-{$booking->reference}.pdf";
    }

    /**
     * The folder with Manrope: TrueType for dompdf, WOFF2 for the page. With forward slashes, as dompdf reads the
     * path from CSS.
     */
    public static function fontPath(string $file = ''): string
    {
        return str_replace('\\', '/', dirname(__DIR__).'/Resources/fonts'.($file === '' ? '' : "/{$file}"));
    }
}
