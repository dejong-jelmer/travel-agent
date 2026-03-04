<?php

namespace App\DTO;

use Money\Money;

readonly class TripPriceData
{
    public function __construct(
        public int $tripPriceId,
        public Money $perPerson,
        public Money $singleSupplement,
        public Money $baseTotal,
        public Money $grandTotal,
        public array $feesAndFunds
    ) {}
}
