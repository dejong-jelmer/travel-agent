<?php

namespace App\Mail;

use App\Models\TripRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TripRequestNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public TripRequest $tripRequest
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('trip_request.mail.notification_subject', [
                'trip' => $this->tripRequest->trip->name,
                'name' => $this->tripRequest->name,
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin.trip-request-notification',
            text: 'emails.text.admin.trip-request-notification',
            with: ['tripRequest' => $this->tripRequest]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
