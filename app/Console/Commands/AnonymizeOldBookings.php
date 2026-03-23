<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\BookingChange;
use App\Models\BookingContact;
use App\Models\BookingTraveler;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AnonymizeOldBookings extends Command
{
    protected $signature = 'bookings:anonymize
                            {--dry-run : Show what would be anonymized without making changes}
                            {--years=7 : Number of years retention period}';

    protected $description = 'Anonymize bookings whose retention period has expired';

    public function handle(): int
    {
        $years = (int) $this->option('years');
        $dryRun = $this->option('dry-run');
        $cutoffDate = now()->subYears($years);

        $bookings = Booking::query()
            ->whereNull('anonymized_at')
            ->where('created_at', '<', $cutoffDate)
            ->get();

        if ($bookings->isEmpty()) {
            $this->info('No bookings found to anonymize.');

            return self::SUCCESS;
        }

        $this->info(sprintf(
            '%s %d booking(s) older than %d year(s) (before %s).',
            $dryRun ? '[DRY RUN] Would anonymize:' : 'Anonymizing',
            $bookings->count(),
            $years,
            $cutoffDate->format('Y-m-d'),
        ));

        if ($dryRun) {
            $bookings->each(function (Booking $booking): void {
                $this->line("  - Booking #{$booking->id} ({$booking->created_at->format('Y-m-d')})");
            });

            return self::SUCCESS;
        }

        $bookingIds = $bookings->pluck('id');

        try {
            DB::transaction(function () use ($bookingIds) {
                Booking::whereIn('id', $bookingIds)
                    ->update([
                        'anonymized_at' => now(),
                        'internal_notes' => null,
                    ]);

                BookingTraveler::whereIn('booking_id', $bookingIds)->delete();
                BookingContact::whereIn('booking_id', $bookingIds)->delete();

                // Delete all change logs of traveler and contact data for these bookings
                BookingChange::whereIn('booking_id', $bookingIds)
                    ->where(function ($query) {
                        $query->whereIn('model_type', [
                            BookingTraveler::class,
                            BookingContact::class,
                        ])->orWhere('field', 'internal_notes');
                    })
                    ->delete();
            });
        } catch (\Throwable $e) {
            $this->error("Anonymization failed: {$e->getMessage()}");

            return self::FAILURE;
        }

        $this->info("{$bookings->count()} booking(s) successfully anonymized.");

        return self::SUCCESS;
    }
}
