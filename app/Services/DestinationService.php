<?php

namespace App\Services;

use App\Models\Destination;

class DestinationService
{
    /**
     * Create a country-level destination as fallback when a region destination
     * is created and no country-level destination exists yet for that country.
     * The travel_info is stored on the country-level so region destinations
     * can inherit it via the Destination model's travelInfo accessor.
     */
    public static function createFallbackDestination(array $validated): bool
    {
        if (
            empty($validated['region'])
            || Destination::where('country_code', $validated['country_code'])
                ->whereNull('region')
                ->exists()
        ) {
            return false;
        }

        Destination::create([
            'country_code' => $validated['country_code'],
            'name' => CountryService::getTranslatedCountryName($validated['country_code']),
            'region' => null,
            'travel_info' => $validated['travel_info'],
        ]);

        return true;
    }
}
