<?php

namespace App\Enums\Trip;

use App\Enums\Traits\HasTranslatableLabel;

enum ItemCategory: string
{
    use HasTranslatableLabel;

    case GeneralInclusions = 'general_inclusions';
    case Transport = 'transport';
    case Accommodation = 'accommodation';
    case AdditionalCost = 'additional_cost';
    case CostsToConsider = 'costs_to_consider';

    protected function getLabelKey(): string
    {
        return 'trip.item.category';
    }
}
