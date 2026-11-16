<?php

namespace App\Services;

use App\DTO\TripPriceData;
use App\Enums\SettingKey;
use App\Exceptions\NoPriceAvailableException;
use App\Models\Departure;
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

        $priceRow = $this->resolvePriceRow($trip, $departureDate);

        if (! $priceRow) {
            throw NoPriceAvailableException::for($trip, $departureDate);
        }

        return $this->price(
            tripPriceId: $priceRow->id,
            perPerson: new Money((int) $priceRow->base_price_pp, $currency),
            singleSupplementPerRoom: new Money((int) $priceRow->single_supplement, $currency),
            persons: $persons,
            // A traveler on their own has a room to themselves
            singleRooms: $persons === 1 ? 1 : 0,
        );
    }

    /**
     * The price of places on a departure of a group trip.
     *
     * The departure's own price per person and single supplement go first. Each one that is not set falls back on
     * the price of the trip for the day of departure. The supplement is charged per single room.
     *
     * @throws \App\Exceptions\NoPriceAvailableException if neither the departure nor the trip has a price per person
     */
    public function forDeparture(Departure $departure, int $persons, int $singleRooms = 0): TripPriceData
    {
        $currency = new Currency(self::CURRENCY);
        $departsOn = Carbon::instance($departure->departs_on);

        $priceRow = $departure->price_per_person === null || $departure->single_supplement === null
            ? $this->resolvePriceRow($departure->trip, $departsOn)
            : null;

        $perPerson = $departure->price_per_person ?? $priceRow?->base_price_pp;

        if ($perPerson === null) {
            throw NoPriceAvailableException::for($departure->trip, $departsOn);
        }

        return $this->price(
            // Only a price per person that comes from the trip ties the price to that row
            tripPriceId: $departure->price_per_person === null ? $priceRow->id : null,
            perPerson: new Money((int) $perPerson, $currency),
            singleSupplementPerRoom: new Money((int) ($departure->single_supplement ?? $priceRow->single_supplement ?? 0), $currency),
            persons: $persons,
            singleRooms: $singleRooms,
        );
    }

    /**
     * Add up a price: the travelers, the single rooms, and the fees and funds once per booking.
     */
    private function price(?int $tripPriceId, Money $perPerson, Money $singleSupplementPerRoom, int $persons, int $singleRooms): TripPriceData
    {
        $feesAndFunds = $this->feesAndFunds->asMoney();

        $supplement = $singleSupplementPerRoom->multiply($singleRooms);
        $baseTotal = $perPerson->multiply($persons);

        $grandTotal = $baseTotal->add($supplement)
            ->add($feesAndFunds[SettingKey::BookingFee->value])
            ->add($feesAndFunds[SettingKey::GuaranteeFund->value])
            ->add($feesAndFunds[SettingKey::EmergencyFund->value]);

        return new TripPriceData(
            tripPriceId: $tripPriceId,
            perPerson: $perPerson,
            singleSupplement: $supplement,
            baseTotal: $baseTotal,
            grandTotal: $grandTotal,
            feesAndFunds: $feesAndFunds,
            singleSupplementPerRoom: $singleSupplementPerRoom,
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
