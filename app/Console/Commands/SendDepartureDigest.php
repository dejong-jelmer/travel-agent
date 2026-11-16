<?php

namespace App\Console\Commands;

use App\Mail\AdminDepartureDigestMail;
use App\Models\Departure;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendDepartureDigest extends Command
{
    protected $signature = 'departures:send-digest
                            {--dry-run : Show which departures would be reported without sending the mail}';

    protected $description = 'Mail the admin the departures that need a decision: due soon below the minimum, overdue, or waiting for the guarantee';

    public function handle(): int
    {
        // The same rules as the overview in the admin, so the mail and the page never disagree
        $departures = Departure::upcoming()
            ->withParticipantCounts()
            ->with('trip')
            ->orderBy('decision_date')
            ->get()
            ->filter(fn (Departure $departure) => $departure->needsAttention())
            ->values();

        if ($departures->isEmpty()) {
            $this->info('No departures need attention.');

            return self::SUCCESS;
        }

        foreach ($departures as $departure) {
            $this->line(sprintf(
                '  - %s, %s: %s (%d confirmed, minimum %d, decision date %s)',
                $departure->trip->name,
                $departure->departs_on->format('Y-m-d'),
                match (true) {
                    $departure->isDecisionOverdue() => 'decision overdue',
                    $departure->awaitsGuarantee() => 'minimum reached, not guaranteed',
                    default => 'decision due soon, minimum not reached',
                },
                $departure->confirmedParticipants(),
                $departure->min_participants,
                $departure->decision_date->format('Y-m-d'),
            ));
        }

        if ($this->option('dry-run')) {
            $this->info(sprintf('[DRY RUN] Would report %d departure(s).', $departures->count()));

            return self::SUCCESS;
        }

        $address = config('booking.mail');

        if (blank($address)) {
            $this->error('No address to send the digest to: set BOOKING_MAIL or CONTACT_MAIL.');

            return self::FAILURE;
        }

        Mail::to($address)->send(new AdminDepartureDigestMail($departures));

        $this->info(sprintf('Reported %d departure(s) to %s.', $departures->count(), $address));

        return self::SUCCESS;
    }
}
