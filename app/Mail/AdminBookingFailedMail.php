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

    public string $errorMessage;
    public string $errorContext;
    public ?array $bookingDetails;

    public function __construct(BookingFailed $event)
    {
        $this->errorMessage = $event->getErrorMessage();
        $this->errorContext = $event->getErrorContext();
        $this->bookingDetails = $event->getBookingDetails();
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Mislukte Boeking: {$this->bookingDetails['trip_name']}",
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
