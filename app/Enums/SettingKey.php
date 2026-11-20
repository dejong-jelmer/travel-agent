<?php

namespace App\Enums;

enum SettingKey: string
{
    case BookingSeasonEnd = 'booking_season_end';
    case BookingFee = 'booking_fee';
    case GuaranteeFund = 'guarantee_fund';
    case EmergencyFund = 'emergency_fund';
    case DefaultBookingMarginBasisPoints = 'default_booking_margin_basis_points';
    case DefaultBookingMarginAmount = 'default_booking_margin_amount';
    case DefaultBookingFeePerPerson = 'default_booking_fee_per_person';

    /**
     * The margins a new booking starts with. They are internal, so they stay
     * out of the settings that every page receives, the public ones included.
     *
     * @return array<int, self>
     */
    public static function defaultMargins(): array
    {
        return [
            self::DefaultBookingMarginBasisPoints,
            self::DefaultBookingMarginAmount,
            self::DefaultBookingFeePerPerson,
        ];
    }
}
