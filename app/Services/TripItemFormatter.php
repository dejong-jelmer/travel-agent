<?php

namespace App\Services;

use App\Enums\Trip\ItemType;
use Illuminate\Support\Collection;

class TripItemFormatter
{
    /**
     * Format trip items into a structured array for frontend consumption.
     *
     * @param  Collection  $items  Collection of trip items with category and item properties
     * @param  ItemType  $type  The type of items (Inclusion or Exclusion)
     * @return array Array with type_label and formatted categories
     */
    public static function format(Collection $items, ItemType $type): array
    {
        $grouped = $items->groupBy('category');

        return [
            'type_label' => $type->label(),
            'categories' => $grouped->map(function ($categoryItems) {
                return [
                    'label' => $categoryItems->first()->category_label,
                    'items' => $categoryItems->pluck('item')->all(),
                ];
            })->values()->all(),
        ];
    }
}
