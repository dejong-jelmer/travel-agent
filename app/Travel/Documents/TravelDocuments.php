<?php

namespace App\Travel\Documents;

use App\Models\Booking;
use App\Travel\Contracts\BookingDetails;
use App\Travel\Data\TravelDocumentData;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdf;

/**
 * The travel documents of a booking: the schedule, the accommodations and the practical information, in one PDF and
 * on the page behind the share link.
 */
class TravelDocuments
{
    public function __construct(private readonly BookingDetails $details) {}

    public function for(Booking $booking): TravelDocumentData
    {
        return TravelDocumentData::fromBooking($booking, $this->details->title($booking), $this->details->travelerNames($booking));
    }

    /**
     * With font subsetting, which laravel-dompdf leaves off: the PDF then holds only the glyphs it uses, and shrinks
     * from over a megabyte to a size that suits a download on the road.
     */
    public function pdf(TravelDocumentData $document): DomPdf
    {
        return Pdf::loadView('travel::documents.pdf', ['document' => $document])
            ->setPaper('a4')
            ->setOption('isFontSubsettingEnabled', true);
    }

    public function filename(Booking $booking): string
    {
        return "reisdocumenten-{$booking->reference}.pdf";
    }
}
