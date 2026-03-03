<?php

namespace App\Enums\Trip;

use App\Enums\Traits\HasTranslatableLabel;
use App\Enums\Traits\Selectable;

enum PriceLabel: string
{
    use HasTranslatableLabel,
        Selectable;

    case LowSeason = 'low_season';
    case HighSeason = 'high_season';
    case MidSeason = 'mid_season';

    protected function getLabelKey(): string
    {
        return 'enum.price_label';
    }
}
