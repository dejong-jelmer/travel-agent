<?php

namespace App\Travel\Enums;

/**
 * `TransitMode` and `LegType` overlap
 * `LegType` takes precedence during rendering, as it also covers transfers and walks,
 *  which have no line at all.
 * `TransitMode` exists solely to organize the library.
 */
enum TransitMode: string
{
    case Train = 'train';
    case Metro = 'metro';
    case Tram = 'tram';
    case Bus = 'bus';
    case Ferry = 'ferry';

    /**
     * The Dutch name, as the library shows it.
     */
    public function label(): string
    {
        return match ($this) {
            self::Train => 'Trein',
            self::Metro => 'Metro',
            self::Tram => 'Tram',
            self::Bus => 'Bus',
            self::Ferry => 'Veerboot',
        };
    }
}
