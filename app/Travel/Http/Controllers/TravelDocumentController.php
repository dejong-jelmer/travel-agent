<?php

namespace App\Travel\Http\Controllers;

use App\Models\Booking;
use App\Travel\Documents\TravelDocuments;
use Illuminate\Http\Response;

/**
 * The PDF of a booking's travel documents, for the admin to check before sharing it.
 */
class TravelDocumentController
{
    public function show(Booking $booking, TravelDocuments $documents): Response
    {
        return $documents->pdf($documents->for($booking))->stream($documents->filename($booking));
    }
}
