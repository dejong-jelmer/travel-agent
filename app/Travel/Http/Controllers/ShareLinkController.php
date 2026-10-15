<?php

namespace App\Travel\Http\Controllers;

use App\Models\Booking;
use App\Travel\Actions\CreateShareLink;
use App\Travel\Actions\DeleteShareLink;
use Illuminate\Http\RedirectResponse;

/**
 * The share link of a booking: made, renewed by making it again, and revoked.
 */
class ShareLinkController
{
    public function store(Booking $booking, CreateShareLink $createShareLink): RedirectResponse
    {
        $createShareLink->handle($booking);

        return back()->with('success', 'Deellink gemaakt.');
    }

    public function destroy(Booking $booking, DeleteShareLink $deleteShareLink): RedirectResponse
    {
        $deleteShareLink->handle($booking);

        return back()->with('success', 'Deellink ingetrokken.');
    }
}
