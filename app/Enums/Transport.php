<?php

namespace App\Enums;

use App\Enums\Traits\Selectable;

enum Transport: string
{
    use Selectable;
    case Train = 'train';
    case Boat = 'boat';
    case Bus = 'bus';
    case Transfer = 'transfer';
    case Airplane = 'airplane';
}
