<?php

namespace App\Enums\Trip;

use App\Enums\Traits\HasTranslatableLabel;

enum ItemType: string
{
    use HasTranslatableLabel;

    case Inclusion = 'inclusion';
    case Exclusion = 'exclusion';

    protected function getLabelKey(): string
    {
        return 'trip.item.type';
    }
}
