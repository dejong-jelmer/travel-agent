<?php

namespace App\Services\Validation;

use App\Models\Trip;
use Carbon\Carbon;

class BlockedDateValidator
{
    public function isBlocked(Trip $trip, Carbon $date): bool
    {
        $blocked = $trip->blocked_dates;
        $weekdays = array_map('intval', $blocked['weekdays'] ?? []);
        $dates = $blocked['dates'] ?? [];

        if (in_array($date->dayOfWeek, $weekdays)) {
            return true;
        }

        foreach ($dates as $entry) {
            if (is_string($entry) && $date->toDateString() === $entry) {
                return true;
            }

            if (is_array($entry) && isset($entry['start'], $entry['end'])) {
                if ($date->between(Carbon::parse($entry['start']), Carbon::parse($entry['end']))) {
                    return true;
                }
            }
        }

        return false;
    }
}
