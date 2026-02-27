<?php

namespace App\Enums;

use App\Enums\Traits\HasTranslatableLabel;
use App\Enums\Traits\Selectable;

enum Transport: string
{
    use HasTranslatableLabel,
        Selectable;

    case Train = 'train';
    case Ferry = 'ferry';
    case Bus = 'bus';
    case Taxi = 'taxi';
    case Transfer = 'transfer';
    case Airplane = 'airplane';

    protected function getLabelKey(): string
    {
        return 'trip.transport';
    }
}
