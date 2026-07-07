<?php

namespace App\Services;

use App\Enums\SettingKey;
use App\Enums\Trip\ItemType;
use App\Models\Setting;
use App\Models\Trip;
use App\Models\TripItem;
use Illuminate\Support\Collection;

/**
 * Aggregates trip items from multiple sources (database + config defaults).
 *
 * This service combines trip-specific items stored in the database with
 * default items from configuration, providing a complete collection of
 * items grouped by type.
 */
class TripItemService
{
    /**
     * Aggregate trip items from database and config defaults.
     *
     * Retrieves trip items from the database, combines them with default items
     * from config, and returns them grouped by type. Database items
     * are added to defaults.
     *
     * @param  Trip  $trip  The trip to aggregate items for
     * @return Collection Nested collection: [ItemType label => Collection<TripItem>]
     */
    public static function aggregate(Trip $trip): Collection
    {
        $feeParams = [
            'trip.item.exclusion.fees.booking' => ['amount' => '€'.Setting::get(SettingKey::BookingFee, '27.50')],
            'trip.item.exclusion.fees.guarantee_fund' => ['amount' => '€'.Setting::get(SettingKey::GuaranteeFund, '10')],
            'trip.item.exclusion.fees.emergency_fund' => ['amount' => '€'.Setting::get(SettingKey::EmergencyFund, '2.50')],
        ];

        return self::mergeItems(
            self::getTripItems($trip),
            self::getDefaultItems(config('trip-default-items', []), $feeParams)
        );
    }

    /**
     * Retrieve trip items from database, grouped by type.
     *
     * Fetches all TripItem records for the given trip and groups them
     * using enum labels as keys for better readability in the frontend.
     *
     * @param  Trip  $trip  The trip to retrieve items for
     * @return Collection Nested collection: [ItemType label => Collection<TripItem>]
     */
    private static function getTripItems(Trip $trip): Collection
    {
        return TripItem::where('trip_id', $trip->id)
            ->get()
            ->groupBy(fn (TripItem $item) => $item->type->label());
    }

    /**
     * Convert default items from config into TripItem models.
     *
     * Transforms the raw config array structure into a collection of TripItem
     * models, grouped by type. Item text is translated via __().
     *
     * @param  array  $tripDefaults  Default items from config: [type => [items]]
     * @param  string  $model  The model class to instantiate (defaults to TripItem)
     * @return Collection Nested collection: [ItemType label => Collection<TripItem>]
     */
    private static function getDefaultItems(array $tripDefaults, array $params = [], string $model = TripItem::class): Collection
    {
        return collect($tripDefaults)
            ->mapWithKeys(fn ($items, $type) => [
                ItemType::from($type)->label() => collect($items)
                    ->map(fn ($item) => new $model([
                        'type' => ItemType::from($type),
                        'item' => __($item, $params[$item] ?? []),
                    ])),
            ]);
    }

    /**
     * Merge trip items with defaults, combining both sources.
     *
     * For each type, default items and database items are combined.
     * Database items are added to defaults.
     * Also includes types that only exist in one source.
     *
     * @param  Collection  $tripItems  Items from database
     * @param  Collection  $defaultItems  Items from config
     * @return Collection Merged collection with defaults + database items combined
     */
    private static function mergeItems(Collection $tripItems, Collection $defaultItems): Collection
    {
        $merged = $defaultItems->map(function ($defaultTypeItems, $type) use ($tripItems) {
            if (! $tripItems->has($type)) {
                // Type doesn't exist in tripItems, use defaults
                return $defaultTypeItems;
            }

            // Type exists in both: combine items (defaults + trip items)
            return $defaultTypeItems->merge($tripItems[$type]);
        });

        // Add types that only exist in tripItems
        return $merged->merge($tripItems->diffKeys($defaultItems));
    }

    /**
     * Sync trip items from the validated array.
     *
     * Expected structure after transformation: [
     *   ['type' => 'inclusion', 'item' => 'item text'],
     *   ...
     * ]
     *
     * @param  array  $items  Flat array of trip items
     */
    public function syncTripItems(Trip $trip, array $items): void
    {
        foreach ($items as $itemData) {
            // Skip items without content
            if (empty(trim($itemData['item']))) {
                continue;
            }

            $typeEnum = ItemType::tryFrom($itemData['type']);

            if (! $typeEnum) {
                continue;
            }

            $trip->items()->create([
                'type' => $typeEnum,
                'item' => $itemData['item'],
            ]);
        }
    }
}
