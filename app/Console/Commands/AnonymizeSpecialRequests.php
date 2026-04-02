<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\BookingChange;
use App\Models\BookingTraveler;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AnonymizeSpecialRequests extends Command
{
    protected $signature = 'bookings:anonymize-special-requests
                            {--dry-run : Show what would be anonymized without making changes}
                            {--days= : Number of days after return_date before anonymizing}';

    protected $description = 'Anonymize special requests for bookings whose return date has passed';

    public function handle(): int
    {
        $days = (int) ($this->option('days') ?? config('privacy.booking.special_requests_retention_days', 7));
        $dryRun = $this->option('dry-run');
        $cutoffDate = now()->subDays($days);

        $bookings = Booking::query()
            ->whereNotNull('return_date')
            ->where('return_date', '<', $cutoffDate)
            ->whereNull('anonymized_at')
            ->whereHas('travelers', fn ($query) => $query->whereNotNull('special_requests'))
            ->get();

        if ($bookings->isEmpty()) {
            $this->info('No bookings found with special requests to anonymize.');

            return self::SUCCESS;
        }

        $this->info(sprintf(
            '%s %d booking(s) with return date before %s.',
            $dryRun ? '[DRY RUN] Would anonymize special requests for' : 'Anonymizing special requests for',
            $bookings->count(),
            $cutoffDate->format('Y-m-d'),
        ));

        if ($dryRun) {
            $bookings->each(function (Booking $booking): void {
                $travelerCount = $booking->travelers()->whereNotNull('special_requests')->whereNull('special_requests_anonymized_at')->count();
                $this->line("  - Booking #{$booking->id} ({$booking->reference}) — return: {$booking->return_date->format('Y-m-d')} — {$travelerCount} traveler(s)");
            });

            return self::SUCCESS;
        }

        $bookingIds = $bookings->pluck('id');

        try {
            DB::transaction(function () use ($bookingIds) {
                BookingTraveler::whereIn('booking_id', $bookingIds)
                    ->whereNotNull('special_requests')
                    ->update([
                        'special_requests' => null,
                        'special_requests_anonymized_at' => now(),
                    ]);

                BookingChange::whereIn('booking_id', $bookingIds)
                    ->where('model_type', BookingTraveler::class)
                    ->where('field', 'special_requests')
                    ->delete();
            });
        } catch (\Throwable $e) {
            $this->error("Anonymization failed: {$e->getMessage()}");

            return self::FAILURE;
        }

        $this->info("{$bookings->count()} booking(s) special requests successfully anonymized.");

        return self::SUCCESS;
    }
}
