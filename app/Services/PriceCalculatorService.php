<?php

namespace App\Services;

use App\DTO\TripPriceData;
use App\Enums\SettingKey;
use App\Exceptions\NoPriceAvailableException;
use App\Models\Setting;
use App\Models\Trip;
use App\Models\TripPrice;
use Carbon\Carbon;
use Money\Currency;
use Money\Money;

class PriceCalculatorService
{
    const CURRENCY = 'EUR';

    /**
     * @throws \App\Exceptions\NoPriceAvailableException if the price can't be resolved
     */
    public function forTrip(Trip $trip, int $persons, Carbon $departureDate): TripPriceData
    {
        $currency = new Currency(self::CURRENCY);
        $bookingFee = new Money((int) round(floatval(Setting::get(SettingKey::BookingFee, 0)) * 100), $currency);
        $emergencyFund = new Money((int) round(floatval(Setting::get(SettingKey::EmergencyFund, 0)) * 100), $currency);
        $guaranteeFund = new Money((int) round(floatval(Setting::get(SettingKey::GuaranteeFund, 0)) * 100), $currency);

        $priceRow = $this->resolvePriceRow($trip, $departureDate);

        if (! $priceRow) {
            throw NoPriceAvailableException::for($trip, $departureDate);
        }

        $perPerson = new Money((int) $priceRow->base_price_pp, $currency);
        $supplement = $persons === 1 ? new Money((int) $priceRow->single_supplement, $currency) : new Money(0, $currency);
        $baseTotal = $perPerson->multiply($persons);

        $grandTotal = $baseTotal->add($supplement)->add($bookingFee)->add($guaranteeFund)->add($emergencyFund);

        return new TripPriceData(
            tripPriceId: $priceRow->id,
            perPerson: $perPerson,
            singleSupplement: $supplement,
            total: $baseTotal,
            bookingFee: $bookingFee,
            guaranteeFund: $guaranteeFund,
            emergencyFund: $emergencyFund,
            grandTotal: $grandTotal,
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
