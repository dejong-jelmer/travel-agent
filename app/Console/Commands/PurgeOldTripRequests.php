<?php

namespace App\Console\Commands;

use App\Models\TripRequest;
use Illuminate\Console\Command;

class PurgeOldTripRequests extends Command
{
    protected $signature = 'trip-requests:purge
                            {--dry-run : Show what would be deleted without making changes}
                            {--years= : Number of years retention period}';

    protected $description = 'Delete trip requests without a booking that have exceeded the retention period';

    public function handle(): int
    {
        $years = (int) ($this->option('years') ?? config('privacy.trip_request.retention_years'));
        $dryRun = $this->option('dry-run');
        $cutoffDate = now()->subYears($years);

        $requests = TripRequest::query()
            ->whereNull('booking_id')
            ->where('created_at', '<', $cutoffDate)
            ->get();

        if ($requests->isEmpty()) {
            $this->info('No trip requests found to purge.');

            return self::SUCCESS;
        }

        $this->info(sprintf(
            '%s %d trip request(s) older than %d year(s) (before %s).',
            $dryRun ? '[DRY RUN] Would delete:' : 'Deleting',
            $requests->count(),
            $years,
            $cutoffDate->format('Y-m-d'),
        ));

        if ($dryRun) {
            $requests->each(function (TripRequest $request): void {
                $this->line("  - TripRequest #{$request->id} ({$request->created_at->format('Y-m-d')})");
            });

            return self::SUCCESS;
        }

        $requests->each->delete();

        $this->info("{$requests->count()} trip request(s) successfully deleted.");

        return self::SUCCESS;
    }
}
