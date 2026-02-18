<?php

namespace App\Services;

use App\Enums\Trip\ItemCategory;
use App\Enums\Trip\ItemType;
use App\Models\Trip;
use App\Models\TripItem;
use Illuminate\Support\Collection;

/**
 * Aggregates trip items from multiple sources (database + config defaults).
 *
 * This service combines trip-specific items stored in the database with
 * default items from configuration, providing a complete collection of
 * items grouped by type and category.
 */
class TripItemService
{
    /**
     * Aggregate trip items from database and config defaults.
     *
     * Retrieves trip items from the database, combines them with default items
     * from config, and returns them grouped by type and category. Database items
     * are added to defaults.
     *
     * @param  Trip  $trip  The trip to aggregate items for
     * @return Collection Nested collection: [ItemType label => [ItemCategory label => Collection<TripItem>]]
     */
    public static function aggregate(Trip $trip): Collection
    {
        return self::mergeItems(
            self::getTripItems($trip),
            self::getDefaultItems(config('trip-default-items', []))
        );
    }

    /**
     * Retrieve trip items from database, grouped by type and category.
     *
     * Fetches all TripItem records for the given trip and groups them
     * using enum labels as keys for better readability in the frontend.
     *
     * @param  Trip  $trip  The trip to retrieve items for
     * @return Collection Nested collection: [ItemType label => [ItemCategory label => Collection<TripItem>]]
     */
    private static function getTripItems(Trip $trip): Collection
    {
        return TripItem::where('trip_id', $trip->id)
            ->get()
            ->groupBy(['type', 'category'])
            ->mapWithKeys(
                fn ($categories, $type) => [
                    ItemType::from($type)->label() => $categories->mapWithKeys(fn ($items, $category) => [
                        ItemCategory::from($category)->label() => $items,
                    ]),
                ]
            );
    }

    /**
     * Convert default items from config into TripItem models.
     *
     * Transforms the raw config array structure into a collection of TripItem
     * models, grouped by type and category. Item text is translated via __().
     *
     * @param  array  $tripDefaults  Default items from config: [type => [category => [items]]]
     * @param  string  $model  The model class to instantiate (defaults to TripItem)
     * @return Collection Nested collection: [ItemType label => [ItemCategory label => Collection<TripItem>]]
     */
    private static function getDefaultItems(array $tripDefaults, string $model = TripItem::class): Collection
    {
        return collect($tripDefaults)
            ->mapWithKeys(fn ($categories, $type) => [
                ItemType::from($type)->label() => collect($categories)
                    ->mapWithKeys(fn ($items, $category) => [
                        ItemCategory::from($category)->label() => collect($items)
                            ->map(fn ($item) => new $model([
                                'type' => ItemCategory::from($category)->type(),
                                'category' => $category,
                                'item' => __($item),
                            ])),
                    ]),
            ]);
    }

    /**
     * Merge trip items with defaults, combining both sources.
     *
     * For each type+category combination, default items and database items
     * are combined. Database items are added to defaults.
     * Also includes categories/types that only exist in one source.
     *
     * @param  Collection  $tripItems  Items from database
     * @param  Collection  $defaultItems  Items from config
     * @return Collection Merged collection with defaults + database items combined
     */
    private static function mergeItems(Collection $tripItems, Collection $defaultItems): Collection
    {
        $merged = $defaultItems->map(function ($defaultCategories, $type) use ($tripItems) {
            if (! $tripItems->has($type)) {
                // Type doesn't exist in tripItems, use defaults
                return $defaultCategories;
            }

            // Type exists in both: merge categories
            return $defaultCategories->map(function ($defaultCategoryItems, $category) use ($tripItems, $type) {
                if (! $tripItems[$type]->has($category)) {
                    // Category doesn't exist in tripItems, use defaults
                    return $defaultCategoryItems;
                }

                // Category exists in both: combine items (defaults + trip items)
                return $defaultCategoryItems->merge($tripItems[$type][$category]);
            })->merge($tripItems[$type]->diffKeys($defaultCategories));
        });

        // Add types that only exist in tripItems
        return $merged->merge($tripItems->diffKeys($defaultItems));
    }

    /**
     * Sync trip items from the validated array.
     *
     * Expected structure after transformation: [
     *   ['type' => 'inclusion', 'category' => 'transport', 'item' => 'item text'],
     *   ...
     * ]
     *
     * @param  array  $items  Flat array of trip items
     */
    public static function syncTripItems(Trip $trip, array $items): void
    {
        foreach ($items as $itemData) {
            // Skip items without content
            if (empty(trim($itemData['item']))) {
                continue;
            }

            // Category is already a value (e.g., 'transport'), not a label
            $categoryEnum = ItemCategory::tryFrom($itemData['category']);

            if (! $categoryEnum) {
                continue; // Skip if category not found
            }

            $trip->items()->create([
                'type' => $categoryEnum->type(), // Get type from category
                'category' => $categoryEnum,
                'item' => $itemData['item'],
            ]);
        }
    }
}
