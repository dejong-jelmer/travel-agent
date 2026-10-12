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
}
