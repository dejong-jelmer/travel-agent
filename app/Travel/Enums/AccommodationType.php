<?php

namespace App\Travel\Enums;

enum AccommodationType: string
{
    case Hotel = 'hotel';
    case BedAndBreakfast = 'bed_and_breakfast';
    case GuestHouse = 'guest_house';
    case Pension = 'pension';
    case Apartment = 'apartment';
    case Hostel = 'hostel';

    /**
     * The Dutch name, as shown in the travel documents.
     */
    public function label(): string
    {
        return match ($this) {
            self::Hotel => 'Hotel',
            self::BedAndBreakfast => 'Bed & breakfast',
            self::GuestHouse => 'Guesthouse',
            self::Pension => 'Pension',
            self::Apartment => 'Appartement',
            self::Hostel => 'Hostel',
        };
    }
}
