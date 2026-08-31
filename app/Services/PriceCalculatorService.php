<?php

namespace App\Services;

use App\DTO\TripPriceData;
use App\Enums\SettingKey;
use App\Exceptions\NoPriceAvailableException;
use App\Models\Trip;
use App\Models\TripPrice;
use App\Support\MoneyHelper;
use Carbon\Carbon;
use Money\Currency;
use Money\Money;

class PriceCalculatorService
{
    const CURRENCY = 'EUR';

    public function __construct(private FeesAndFundsService $feesAndFunds) {}

    /**
     * @throws \App\Exceptions\NoPriceAvailableException if the price can't be resolved
     */
    public function forTrip(Trip $trip, int $persons, Carbon $departureDate): TripPriceData
    {
        $currency = new Currency(self::CURRENCY);

        $feesAndFunds = $this->feesAndFunds->asMoney();

        $priceRow = $this->resolvePriceRow($trip, $departureDate);

        if (! $priceRow) {
            throw NoPriceAvailableException::for($trip, $departureDate);
        }

        $perPerson = new Money((int) $priceRow->base_price_pp, $currency);
        $supplement = $persons === 1 ? new Money((int) $priceRow->single_supplement, $currency) : new Money(0, $currency);
        $baseTotal = $perPerson->multiply($persons);

        $grandTotal = $baseTotal->add($supplement)
            ->add($feesAndFunds[SettingKey::BookingFee->value])
            ->add($feesAndFunds[SettingKey::GuaranteeFund->value])
            ->add($feesAndFunds[SettingKey::EmergencyFund->value]);

        return new TripPriceData(
            tripPriceId: $priceRow->id,
            perPerson: $perPerson,
            singleSupplement: $supplement,
            baseTotal: $baseTotal,
            grandTotal: $grandTotal,
            feesAndFunds: $feesAndFunds
        );
    }

    public function formatAmount(Money $money): string
    {
        return bcdiv((string) $money->getAmount(), (string) MoneyHelper::CENTS_PER_UNIT, 2);
    }

    /**
     * Get formatted fees and funds
     *
     * @return array<string, string> Associative array with setting keys and formatted amounts
     */
    public function getFormattedFeesAndFunds(): array
    {
        return array_map(
            fn (Money $ff) => $this->formatAmount($ff),
            $this->feesAndFunds->asMoney()
        );
    }

    private function resolvePriceRow(Trip $trip, Carbon $departureDate): ?TripPrice
    {
        return TripPrice::query()
            ->where('trip_id', $trip->id)
            ->where('valid_from', '<=', $departureDate)
            ->where('valid_until', '>=', $departureDate)
            ->orderBy('valid_from', 'desc')
            ->first();
    }
}
