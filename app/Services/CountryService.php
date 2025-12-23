<?php

namespace App\Services;

class CountryService
{
    /**
     * European countries with their locale ISO codes for faker
     *
     * @return array<string, string> Country name => Locale ISO code
     */
    public static function europeanCountries(): array
    {
        return config('countries.europe', []);
    }

    /**
     * Get all country names
     *
     * @return array<string>
     */
    public static function names(): array
    {
        return array_keys(self::europeanCountries());
    }

    /**
     * Get locale for a country name
     *
     * @return string Locale ISO code (defaults to nl_NL if not found)
     */
    public static function getLocale(string $countryName): string
    {
        return self::europeanCountries()[$countryName] ?? 'nl_NL';
    }

    /**
     * Get random country with its locale
     *
     * @return array{name: string, locale: string}
     */
    public static function random(): array
    {
        $countries = self::europeanCountries();
        $name = array_rand($countries);

        return [
            'name' => $name,
            'locale' => $countries[$name],
        ];
    }

    /**
     * Static pool for unique country generation
     *
     * @var array<int, string>|null
     */
    private static ?array $availableCountries = null;

    /**
     * Get a unique country name
     * Uses a static pool to ensure uniqueness across multiple calls
     *
     * @return string Unique country name
     */
    public static function uniqueRandomName(): string
    {
        // Initialize the list of available countries on first use
        if (self::$availableCountries === null) {
            self::$availableCountries = self::names();
        }

        // If we've run out of countries, reset the list
        if (empty(self::$availableCountries)) {
            self::$availableCountries = self::names();
        }

        // Pick and remove a random country from the available list
        $randomKey = array_rand(self::$availableCountries);

        $countryName = self::$availableCountries[$randomKey];
        unset(self::$availableCountries[$randomKey]);

        return $countryName;
    }

    /**
     * Reset the unique country pool
     * Useful for tests to ensure clean state between test cases
     */
    public static function resetUniquePool(): void
    {
        self::$availableCountries = null;
    }
}
