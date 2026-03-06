<?php

namespace App\Enums;

enum SettingKey: string
{
    case BookingSeasonEnd = 'booking_season_end';
    case BookingFee = 'booking_fee';
    case GuaranteeFund = 'guarantee_fund';
    case EmergencyFund = 'emergency_fund';
}
