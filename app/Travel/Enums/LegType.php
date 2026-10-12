<?php

namespace App\Travel\Enums;

enum LegType: string
{
    case Train = 'train';
    case Metro = 'metro';
    case Tram = 'tram';
    case Bus = 'bus';
    case Ferry = 'ferry';
    case Taxi = 'taxi';
    case Transfer = 'transfer';
    case Walk = 'walk';

    /**
     * The Dutch name, as shown in the travel documents.
     */
    public function label(): string
    {
        return match ($this) {
            self::Train => 'Trein',
            self::Metro => 'Metro',
            self::Tram => 'Tram',
            self::Bus => 'Bus',
            self::Ferry => 'Veerboot',
            self::Taxi => 'Taxi',
            self::Transfer => 'Overstap',
            self::Walk => 'Lopen',
        };
    }
}
