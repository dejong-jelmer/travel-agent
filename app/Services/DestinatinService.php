<?php

namespace App\Services;

use App\Models\Destination;

class DestinatinService
{
    public static function fallbackExists(array $validated): bool
    {
        return
            ! empty($validated['region'])
            && ! Destination::where('country_code', $validated['country_code'])
                ->whereNull('region')
                ->exists();
    }

    public static function createFallback(array $fields): self
    {
        return Destination::create([
            'country_code' => $fields['country_code'],
            'name' => CountryService::getTranslatedCountryName($fields['country_code']),
            'region' => null,
            'travel_info' => $fields['travel_info'],
        ]);
    }
}
