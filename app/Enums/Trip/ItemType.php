<?php

namespace App\Enums\Trip;

use App\Enums\Traits\HasTranslatableLabel;
use App\Enums\Traits\Selectable;

enum ItemType: string
{
    use HasTranslatableLabel,
        Selectable;

    case Inclusion = 'inclusion';
    case Exclusion = 'exclusion';

    protected function getLabelKey(): string
    {
        return 'trip.item.type';
    }
}
