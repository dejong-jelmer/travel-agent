<?php

namespace App\Services;

use App\Models\Destination;

class DestinationService
{
    public static function fallbackExists(string $countryCode): bool
    {
        return Destination::where('country_code', $countryCode)
            ->whereNull('region')
            ->exists();
    }
}
