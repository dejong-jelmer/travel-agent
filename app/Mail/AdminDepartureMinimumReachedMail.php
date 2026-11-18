<?php

namespace App\Mail;

use App\Models\Departure;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminDepartureMinimumReachedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Departure $departure
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('departure.mail.minimum_reached.subject', [
                'trip' => $this->departure->trip->name,
                'date' => $this->departure->departs_on->format('d-m-Y'),
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin.departure-minimum-reached',
            text: 'emails.text.admin.departure-minimum-reached',
            with: ['departure' => $this->departure]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
