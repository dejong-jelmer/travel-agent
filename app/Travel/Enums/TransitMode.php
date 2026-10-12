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
}
