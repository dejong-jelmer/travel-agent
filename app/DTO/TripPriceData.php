<?php

namespace App\DTO;

use Money\Money;

readonly class TripPriceData
{
    /**
     * @param  int|null  $tripPriceId  The price row of the trip the price comes from, or null when a departure sets its own price
     * @param  Money  $singleSupplement  The supplement charged in total, for all single rooms together
     * @param  array<string, Money>  $feesAndFunds  Keyed by SettingKey value (e.g. 'booking_fee')
     * @param  Money|null  $singleSupplementPerRoom  The supplement for one single room, also when none is charged
     */
    public function __construct(
        public ?int $tripPriceId,
        public Money $perPerson,
        public Money $singleSupplement,
        public Money $baseTotal,
        public Money $grandTotal,
        public array $feesAndFunds,
        public ?Money $singleSupplementPerRoom = null,
    ) {}
}
