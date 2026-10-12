<?php

namespace App\Travel\Http\Controllers;

use App\Models\Booking;
use App\Travel\Documents\TravelDocuments;
use App\Travel\Models\ShareLink;
use Illuminate\Http\Response;

/**
 * The travel documents behind a share link: a page for the phone, and the PDF to keep. A link that was revoked, has
 * expired or belongs to a deleted booking is not found, so the response says nothing about the booking.
 */
class SharedDocumentController
{
    public function show(ShareLink $shareLink, TravelDocuments $documents): Response
    {
        return response()
            ->view('travel::documents.shared', [
                'document' => $documents->for($this->bookingOf($shareLink)),
                'pdfUrl' => route('travel.documents.shared.pdf', $shareLink),
            ])
            ->header('X-Robots-Tag', 'noindex, nofollow');
    }

    public function pdf(ShareLink $shareLink, TravelDocuments $documents): Response
    {
        $booking = $this->bookingOf($shareLink);

        return $documents->pdf($documents->for($booking))
            ->download($documents->filename($booking))
            ->header('X-Robots-Tag', 'noindex, nofollow');
    }

    private function bookingOf(ShareLink $shareLink): Booking
    {
        $booking = $shareLink->booking;

        abort_if($booking === null || $shareLink->isExpired(), 404);

        return $booking;
    }
}
