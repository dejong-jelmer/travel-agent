<?php

namespace App\Travel\Http\Controllers;

use App\Models\Booking;
use App\Travel\Documents\TravelDocuments;
use App\Travel\Models\ShareLink;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * The travel documents behind a share link: a page per part for the phone, and the PDF to keep. A link that was
 * revoked, has expired or belongs to a deleted booking is not found, so the response says nothing about the booking.
 */
class SharedDocumentController
{
    /**
     * The page of the Reisschema part, which the link itself opens.
     */
    public function show(ShareLink $shareLink, TravelDocuments $documents): Response
    {
        return $this->page($shareLink, $documents, 'reisschema');
    }

    public function stays(ShareLink $shareLink, TravelDocuments $documents): Response
    {
        return $this->page($shareLink, $documents, 'verblijf');
    }

    public function practical(ShareLink $shareLink, TravelDocuments $documents): Response
    {
        return $this->page($shareLink, $documents, 'praktisch');
    }

    public function pdf(ShareLink $shareLink, TravelDocuments $documents): Response
    {
        $booking = $this->bookingOf($shareLink);

        return $documents->pdf($documents->for($booking))
            ->download($documents->filename($booking))
            ->header('X-Robots-Tag', 'noindex, nofollow');
    }

    /**
     * Manrope for the pages, from the module itself rather than from Google, so a traveler's visit goes nowhere else.
     */
    public function font(string $font): BinaryFileResponse
    {
        return response()->file(TravelDocuments::fontPath($font), [
            'Content-Type' => 'font/woff2',
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }

    /**
     * @param  key-of<TravelDocuments::PARTS>  $part
     */
    private function page(ShareLink $shareLink, TravelDocuments $documents, string $part): Response
    {
        $tabs = [
            'reisschema' => route('travel.documents.shared', $shareLink),
            'verblijf' => route('travel.documents.shared.stays', $shareLink),
            'praktisch' => route('travel.documents.shared.practical', $shareLink),
        ];

        return response()
            ->view('travel::documents.shared', [
                'document' => $documents->for($this->bookingOf($shareLink)),
                'active' => $part,
                'tabs' => $tabs,
                'stayHref' => fn (int $number) => "{$tabs['verblijf']}#verblijf-{$number}",
                'pdfUrl' => route('travel.documents.shared.pdf', $shareLink),
            ])
            ->header('X-Robots-Tag', 'noindex, nofollow');
    }

    private function bookingOf(ShareLink $shareLink): Booking
    {
        $booking = $shareLink->booking;

        abort_if($booking === null || $shareLink->isExpired(), 404);

        return $booking;
    }
}
