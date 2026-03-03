<?php

namespace App\Enums\Trip;

use App\Enums\Traits\HasTranslatableLabel;
use App\Enums\Traits\Selectable;

enum PriceLabel: string
{
    use HasTranslatableLabel,
        Selectable;

    case Low_season = 'low_season';
    case High_season = 'high_season';
    case Mid_season = 'mid_season';

    protected function getLabelKey(): string
    {
        return 'enum.price_label';
    }
}
