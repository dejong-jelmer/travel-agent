<?php

namespace App\Support;

class MoneyHelper
{
    public const CENTS_PER_UNIT = 100;

    /**
     * Convert a decimal amount (euros) to cents using precise string arithmetic,
     * avoiding floating-point rounding errors (e.g. 1.05 * 100 = 104.9999...).
     */
    public static function toCents(float|string $amount): int
    {
        return (int) bcmul((string) $amount, (string) self::CENTS_PER_UNIT, 0);
    }
}
