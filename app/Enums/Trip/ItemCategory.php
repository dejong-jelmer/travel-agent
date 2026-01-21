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
            self::Transport, self::Accommodation, self::AdditionalCost => true,
            self::GeneralInclusions, self::CostsToConsider => false,
        };
    }

    public function type(): ItemType
    {
        return match ($this) {
            self::GeneralInclusions, self::Transport, self::Accommodation => ItemType::Inclusion,
            self::AdditionalCost, self::CostsToConsider => ItemType::Exclusion,
        };
    }
}
