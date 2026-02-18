<?php

namespace App\Services;

use App\Http\Resources\CountryResource;
use App\Models\Country;

class CountryService
{
    /**
     * Static pool for unique country generation
     *
     * @var array<int, array>|null
     */
    private static ?array $availableCountries = null;

    /**
     * European countries with their country codes, regions, and locales
     *
     * @return array<int, array{country_code: string, region: string|null, name: string, locale: string}>
     */
    public static function europeanCountries(): array
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
     * Get a unique random country with all data
     * Uses a static pool to ensure uniqueness across multiple calls
     *
     * @return array{country_code: string, region: string|null, name: string, locale: string}
     */
    public static function uniqueRandom(): array
    {
        // Initialize the list of available countries on first use
        if (self::$availableCountries === null) {
            self::$availableCountries = self::europeanCountries();
        }

        // If we've run out of countries, reset the list
        if (empty(self::$availableCountries)) {
            self::$availableCountries = self::europeanCountries();
        }

        // Pick and remove a random country from the available list
        $randomKey = array_rand(self::$availableCountries);
        $country = self::$availableCountries[$randomKey];
        unset(self::$availableCountries[$randomKey]);

        return [
            'country_code' => $country['code'],
            'region' => in_array($country['name'], ['Engeland, Schotland, Wales']) ? $country['name'] : null,
            'name' => $country['name'],
            'locale' => self::getLocaleByCountryCode($country['code']),
        ];
    }

    /**
     * Reset the unique country pool
     * Useful for tests to ensure clean state between test cases
     */
    public static function resetUniquePool(): void
    {
        self::$availableCountries = null;
    }

    /**
     * Get the translated country name for a given country code.
     */
    public static function getTranslatedCountryName(string $countryCode): string
    {
        $country = Country::find($countryCode);

        return $country?->getTranslatedName() ?? $countryCode;
    }
}
