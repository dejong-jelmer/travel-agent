<?php

namespace App\DTO;

use App\DTO\Traits\ArrayableDTO;
use App\Enums\Booking\CostCategory;
use Illuminate\Contracts\Support\Arrayable;

class BookingCostItemData implements Arrayable
{
    use ArrayableDTO;

    public function __construct(
        public readonly CostCategory $category,
        public readonly string $label,
        public readonly int $amount_per_person,
        public readonly int $quantity,
        public readonly int $sort_order,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data, int $sortOrder = 0): self
    {
        return new self(
            category: $data['category'] instanceof CostCategory
                ? $data['category']
                : CostCategory::from($data['category']),
            label: $data['label'],
            amount_per_person: (int) $data['amount_per_person'],
            quantity: (int) ($data['quantity'] ?? 1),
            sort_order: (int) ($data['sort_order'] ?? $sortOrder),
        );
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return array<int, self>
     */
    public static function manyFromArray(array $items): array
    {
        return array_map(
            fn (array $item, int $index) => self::fromArray($item, $index),
            array_values($items),
            array_keys(array_values($items)),
        );
    }
}
