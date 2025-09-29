<?php

namespace App\Enums;

use App\Enums\Traits\Selectable;

enum Transport: string
{
    use Selectable;
    case Train = 'Trein';
    case Boat = 'Boot';
    case Bus = 'Bus';
    case Transfer = 'Transfer';
    case Airplane = 'Vliegtuig';
}
