<?php

namespace App\Services;

use App\Http\Resources\CountryResource;
use App\Models\Country;

class DestinationService
{
     /**
     * Static pool for unique destination generation
     *
     * @var array<int, array>|null
     */
    private static ?array $availableDestination = null;

    /**
     * European destinations with their country codes, regions, and locales
     *
     * @return array<int, array{country_code: string, region: string|null, name: string, locale: string}>
     */
    public static function europeanDestination(): array
    {
        return CountryResource::collection(
            Country::whereIn('code', array_keys(config('locales')))->orderBy('name')->get()
        )->resolve();
    }

    /**
     * Get all countries as a resource collection
     */
    public static function countries(): array
    {
        return CountryResource::collection(
            Country::orderBy('name')->get()
        )->resolve();
    }

    /**
     * Get locale by country code and optional region
     *
     * @return string Locale ISO code (defaults to nl_NL if not found)
     */
    public static function getLocaleByCountryCode(string $countryCode): string
    {
        return config("locales.{$countryCode}", 'nl_NL');
    }

    /**
     * Get a unique random destination with all data
     * Uses a static pool to ensure uniqueness across multiple calls
     *
     * @return array{country_code: string, region: string|null, name: string, locale: string}
     */
    public static function uniqueRandom(): array
    {
        // Initialize the list of available destinations on first use
        if (self::$availableDestination === null) {
            self::$availableDestination = self::europeanDestination();
        }

        // If we've run out of destinations, reset the list
        if (empty(self::$availableDestination)) {
            self::$availableDestination = self::europeanDestination();
        }

        // Pick and remove a random destination from the available list
        $randomKey = array_rand(self::$availableDestination);
        $destination = self::$availableDestination[$randomKey];
        unset(self::$availableDestination[$randomKey]);

        return [
            'country_code' => $destination['code'],
            'region' => in_array($destination['name'], ['Engeland, Schotland, Wales']) ? $destination['name'] : null,
            'name' => $destination['name'],
            'locale' => self::getLocaleByCountryCode($destination['code']),
        ];
    }

    /**
     * Reset the unique destination pool
     * Useful for tests to ensure clean state between test cases
     */
    public static function resetUniquePool(): void
    {
        self::$availableDestination = null;
    }
}
