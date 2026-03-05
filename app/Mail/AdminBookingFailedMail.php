<?php

namespace App\Mail;

use App\Events\BookingFailed;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminBookingFailedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public BookingFailed $event) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Mislukte Boeking: {$this->event->bookingDetails['trip_name']}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin.booking-failed',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
