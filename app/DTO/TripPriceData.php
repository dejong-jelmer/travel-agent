<?php

namespace App\DTO;

use Money\Money;

readonly class TripPriceData
{
    public function __construct(
        public int $tripPriceId,
        public Money $perPerson,
        public Money $singleSupplement,
        public Money $total,
        public Money $bookingFee,
        public Money $guaranteeFund,
        public Money $emergencyFund,
        public Money $grandTotal,
    ) {}
}
