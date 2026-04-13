<?php

namespace App\Mail;

use App\Models\TripRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TripRequestConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public TripRequest $tripRequest
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('trip_request.mail.confirmation_subject', [
                'trip' => $this->tripRequest->trip->name,
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.trip-request-confirmation',
            text: 'emails.text.trip-request-confirmation',
            with: ['tripRequest' => $this->tripRequest]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
