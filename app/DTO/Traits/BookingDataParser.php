<?php

namespace App\DTO\Traits;

use App\DTO\BookingContactData;
use App\DTO\BookingCostItemData;
use App\DTO\BookingTravelerData;
use App\Enums\TravelerType;
use App\Models\Trip;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

trait BookingDataParser
{
    /**
     * Parse data from validated request
     */
    protected static function parseValidatedData(array $validated): array
    {
        $travelers = $validated['travelers'] ?? [];
        $adults = $travelers['adults'] ?? [];
        $children = $travelers['children'] ?? [];
        $mainBookerIndex = $validated['main_booker'] ?? null;
        $mainBookerFullName = self::getMainBookerFullName($adults, $mainBookerIndex);
        $mainBooker = ['name' => $mainBookerFullName, 'index' => $mainBookerIndex];

        $adultTravelers = BookingTravelerData::manyFromArray($adults);
        $childTravelers = BookingTravelerData::manyFromArray($children);

        $costItems = array_map(
            fn (BookingCostItemData $item) => $item->toArray(),
            BookingCostItemData::manyFromArray($validated['cost_items'] ?? []),
        );

        $marginBasisPoints = isset($validated['margin_percentage'])
            ? (int) round(((float) $validated['margin_percentage']) * 100)
            : null;

        $marginInPercentage = ! array_key_exists('margin_in_percentage', $validated)
            || (bool) $validated['margin_in_percentage'];

        $marginAmount = isset($validated['margin_amount']) && $validated['margin_amount'] !== ''
            ? (int) $validated['margin_amount']
            : null;

        $feePerPerson = isset($validated['fee_per_person']) && $validated['fee_per_person'] !== ''
            ? (int) $validated['fee_per_person']
            : null;

        $finalPrice = isset($validated['final_price']) && $validated['final_price'] !== ''
            ? (int) $validated['final_price']
            : null;

        return [
            'main_booker' => $mainBooker,
            'travelers' => [
                TravelerType::Adult->value => $adultTravelers,
                TravelerType::Child->value => $childTravelers,
            ],
            'contact' => BookingContactData::fromArray($mainBookerFullName, $validated['contact']),
            'cost_items' => $costItems,
            'margin_basis_points' => $marginBasisPoints,
            'margin_in_percentage' => $marginInPercentage,
            'margin_amount' => $marginAmount,
            'fee_per_person' => $feePerPerson,
            'final_price' => $finalPrice,
        ];
    }

    /**
     * Extract main booker full name from adults array
     */
    protected static function getMainBookerFullName(array $adults, ?int $mainBookerIndex): string
    {
        if ($mainBookerIndex !== null && isset($adults[$mainBookerIndex])) {
            return $adults[$mainBookerIndex]['full_name'] ?? "{$adults[$mainBookerIndex]['first_name']} {$adults[$mainBookerIndex]['last_name']}";
        }

        return 'Unknown';
    }

    /**
     * Find trip by ID with error handling
     */
    protected static function findTrip(?int $tripId): Trip
    {
        if (! $tripId) {
            Log::error('Booking attempt without trip ID');
            throw new ModelNotFoundException('Trip ID is required');
        }

        $trip = Trip::find($tripId);

        if (! $trip) {
            Log::error('Booking attempt for non-existing trip', ['trip_id' => $tripId]);
            throw new ModelNotFoundException('Trip not found');
        }

        return $trip;
    }
}
