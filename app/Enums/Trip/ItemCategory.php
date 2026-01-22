<?php

namespace App\Enums\Trip;

use App\Enums\Traits\HasTranslatableLabel;
use App\Enums\Traits\Selectable;

enum ItemCategory: string
{
    use HasTranslatableLabel,
        Selectable;

    case GeneralInclusions = 'general_inclusions';
    case Transport = 'transport';
    case Accommodation = 'accommodation';
    case AdditionalCost = 'additional_cost';
    case CostsToConsider = 'costs_to_consider';

    protected function getLabelKey(): string
    {
        return 'trip.item.category';
    }

    public function extraOptions(): array
    {
        return [
            'disabled' => ! $this->isCustomizable(),
            'type' => $this->type(),
        ];
    }

    public function isCustomizable(): bool
    {
        return match ($this) {
            self::GeneralInclusions, self::CostsToConsider => false,
            default => true,
        };
    }

    public function type(): ItemType
    {
        return match ($this) {
            self::AdditionalCost, self::CostsToConsider => ItemType::Exclusion,
            default => ItemType::Inclusion,
        };
    }
}
