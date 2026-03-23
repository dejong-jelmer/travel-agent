<?php

namespace App\Console\Commands;

use App\Models\Booking;
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
            $bookings->each(fn(Booking $booking) => $this->line(
                "  - Booking #{$booking->id} ({$booking->created_at->format('Y-m-d')})"
            ));

            return self::SUCCESS;
        }

        $anonymized = 0;

        foreach ($bookings as $booking) {
            try {
                DB::transaction(function () use ($booking) {
                    $booking->update(['anonymized_at' => now()]);
                    $booking->travelers()->delete();
                    $booking->contact()->delete();

                    // Delete all change logs of traveler and contact data for this booking
                    $booking->changes()
                        ->whereIn('model_type', [
                            BookingTraveler::class,
                            BookingContact::class,
                        ])
                        ->delete();
                });

                $anonymized++;
            } catch (\Throwable $e) {
                $this->error("Error on booking #{$booking->id}: {$e->getMessage()}");
            }
        }

        $this->info("{$anonymized} booking(s) successfully anonymized.");

        return self::SUCCESS;
    }
}
