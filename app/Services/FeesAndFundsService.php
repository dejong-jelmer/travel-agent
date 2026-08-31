<?php

namespace App\Services;

use App\Enums\SettingKey;
use App\Models\Setting;
use App\Support\MoneyHelper;
use Money\Currency;
use Money\Money;

/**
 * Reads the booking fee and the guarantee and emergency fund contributions
 * from the application settings.
 *
 * These are charged once per booking, on top of the calculated sales price.
 */
class FeesAndFundsService
{
    const CURRENCY = 'EUR';

    /**
     * The settings that make up the fees and funds, in the order they are shown.
     *
     * @var array<int, SettingKey>
     */
    private const KEYS = [
        SettingKey::BookingFee,
        SettingKey::GuaranteeFund,
        SettingKey::EmergencyFund,
    ];

    /**
     * Current amounts in cents, keyed by setting key.
     *
     * @return array<string, int>
     */
    public function asCents(): array
    {
        $amounts = [];

        foreach (self::KEYS as $key) {
            $amounts[$key->value] = MoneyHelper::toCents(Setting::get($key, 0));
        }

        return $amounts;
    }

    /**
     * Current amounts as Money objects, keyed by setting key.
     *
     * @return array<string, Money>
     */
    public function asMoney(): array
    {
        $currency = new Currency(self::CURRENCY);

        return array_map(
            fn (int $cents) => new Money($cents, $currency),
            $this->asCents(),
        );
    }
}
