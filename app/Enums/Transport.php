<?php

namespace App\Enums;

use App\Enums\Traits\HasTranslatableLabel;
use App\Enums\Traits\Selectable;

enum Transport: string
{
    use HasTranslatableLabel,
        Selectable;

    private const LABEL_KEY = 'itinerary.transport';

    case Train = 'train';
    case Ferry = 'ferry';
    case Bus = 'bus';
    case Taxi = 'taxi';
    case Transfer = 'transfer';
    case Airplane = 'airplane';
}
