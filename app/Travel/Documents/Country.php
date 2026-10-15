<?php

namespace App\Travel\Documents;

use Locale;

/**
 * What the practical information says about a country the trip goes to. Only what holds for the whole country is
 * here; anything that is not known falls back to what holds across Europe, such as 112.
 */
readonly class Country
{
    /**
     * Numbers besides 112 that the travelers will hear about locally, for an ambulance or in the United Kingdom.
     */
    private const EMERGENCY_NUMBERS = [
        'AT' => '112 of 144',
        'CH' => '112 of 144',
        'FR' => '112 of 15',
        'GB' => '999 of 112',
        'IT' => '112 of 118',
    ];

    /**
     * Where a Dutch ID card is enough: the EU, the EEA and Switzerland.
     */
    private const ID_CARD_COUNTRIES = [
        'AT', 'BE', 'BG', 'CH', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GR', 'HR', 'HU', 'IE', 'IS', 'IT',
        'LI', 'LT', 'LU', 'LV', 'MT', 'NL', 'NO', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK',
    ];

    /**
     * Where the European health insurance card (EHIC) gives access to public care: those countries and the United
     * Kingdom.
     */
    private const EHIC_COUNTRIES = [...self::ID_CARD_COUNTRIES, 'GB'];

    /**
     * Where tap water is fine to drink, with the words on the signs at water points: drinking water, and not.
     */
    private const DRINKING_WATER = [
        'AT' => ['Trinkwasser', 'kein Trinkwasser'],
        'DE' => ['Trinkwasser', 'kein Trinkwasser'],
        'ES' => ['agua potable', 'agua no potable'],
        'FR' => ['eau potable', 'eau non potable'],
        'IT' => ['acqua potabile', 'acqua non potabile'],
        'PT' => ['água potável', 'água não potável'],
    ];

    /**
     * @param  array{0: string, 1: string}|null  $drinkingWater  The signs for drinking water and for water that is not.
     */
    public function __construct(
        public string $code,
        public string $name,
        public string $emergencyNumbers,
        public bool $idCardSuffices,
        public bool $ehic,
        public ?array $drinkingWater,
    ) {}

    public static function for(string $code): self
    {
        $code = strtoupper($code);

        return new self(
            code: $code,
            name: class_exists(Locale::class) ? Locale::getDisplayRegion("-{$code}", 'nl') : $code,
            emergencyNumbers: self::EMERGENCY_NUMBERS[$code] ?? '112',
            idCardSuffices: in_array($code, self::ID_CARD_COUNTRIES, true),
            ehic: in_array($code, self::EHIC_COUNTRIES, true),
            drinkingWater: self::DRINKING_WATER[$code] ?? null,
        );
    }

    /**
     * @param  list<string>  $codes
     * @return list<self>
     */
    public static function all(array $codes): array
    {
        return array_map(fn (string $code) => self::for($code), $codes);
    }
}
