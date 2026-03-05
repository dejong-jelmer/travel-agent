<?php

namespace App\DTO;

use Money\Money;

readonly class TripPriceData
{
    /**
     * @param  array<string, Money>  $feesAndFunds  Keyed by SettingKey value (e.g. 'booking_fee')
     */
    public function __construct(
        public int $tripPriceId,
        public Money $perPerson,
        public Money $singleSupplement,
        public Money $baseTotal,
        public Money $grandTotal,
        public array $feesAndFunds
    ) {}
}
