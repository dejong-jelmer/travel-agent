<?php

namespace App\Resources;

class CountryResource
{
    /**
     * European countries with their locale ISO codes for faker
     *
     * @return array<string, string> Country name => Locale ISO code
     */
    public static function europeanCountries(): array
    {
        return [
            'Nederland' => 'nl_NL',
            'België' => 'nl_BE',
            'Frankrijk' => 'fr_FR',
            'Duitsland' => 'de_DE',
            'Oostenrijk' => 'de_AT',
            'Zwitserland' => 'de_CH',
            'Italië' => 'it_IT',
            'Spanje' => 'es_ES',
            'Portugal' => 'pt_PT',
            'Denemarken' => 'da_DK',
            'Zweden' => 'sv_SE',
            'Noorwegen' => 'nb_NO',
            'Polen' => 'pl_PL',
            'Tsjechië' => 'cs_CZ',
            'Hongarije' => 'hu_HU',
            'Slovenië' => 'sl_SI',
            'Kroatië' => 'hr_HR',
            'Griekenland' => 'el_GR',
            'Roemenië' => 'ro_RO',
            'Bulgarije' => 'bg_BG',
        ];
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
     * Get a unique country name (for use in factories/tests)
     * Uses a static pool to ensure uniqueness across multiple calls
     */
    public static function uniqueRandomName(): string
    {
        static $availableCountries = null;

        // Initialize the list of available countries on first use
        if ($availableCountries === null) {
            $availableCountries = self::names();
        }

        // If we've run out of countries, reset the list
        if (empty($availableCountries)) {
            $availableCountries = self::names();
        }

        // Pick and remove a random country from the available list
        $randomKey = array_rand($availableCountries);

        $countryName = $availableCountries[$randomKey];
        unset($availableCountries[$randomKey]);

        return $countryName;
    }
}
