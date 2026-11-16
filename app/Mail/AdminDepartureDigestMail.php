<?php

namespace App\Mail;

use App\Models\Departure;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * The daily overview of the departures that need a decision of the admin, grouped by what has to happen.
 */
class AdminDepartureDigestMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  Collection<int, Departure>  $departures  The departures that need attention, with their trip loaded
     */
    public function __construct(
        public Collection $departures
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: trans_choice('departure.mail.digest.subject', $this->departures->count(), [
                'count' => $this->departures->count(),
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin.departure-digest',
            text: 'emails.text.admin.departure-digest',
            with: ['groups' => $this->groups()]
        );
    }

    /**
     * The departures by what the admin has to do, the most pressing first. Every departure is in one group only.
     *
     * @return array<string, Collection<int, Departure>>
     */
    public function groups(): array
    {
        $overdue = $this->departures->filter(fn (Departure $departure) => $departure->isDecisionOverdue());
        $dueSoon = $this->departures->filter(fn (Departure $departure) => $departure->isDecisionDueSoon());
        $awaitsGuarantee = $this->departures
            ->filter(fn (Departure $departure) => $departure->awaitsGuarantee())
            ->diff($overdue);

        return array_filter([
            'overdue' => $overdue->values(),
            'due_soon' => $dueSoon->values(),
            'awaits_guarantee' => $awaitsGuarantee->values(),
        ], fn (Collection $group) => $group->isNotEmpty());
    }

    public function attachments(): array
    {
        return [];
    }
}
