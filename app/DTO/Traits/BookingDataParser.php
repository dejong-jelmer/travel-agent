<?php

namespace App\DTO\Traits;

use App\DTO\BookingContactData;
use App\DTO\BookingTravelerData;
use App\Enums\Booking\PaymentStatus;
use App\Enums\Booking\Status;
use App\Enums\TravelerType;
use App\Models\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

trait BookingDataParser
{
    /**
     * Parse data from validated request
     */
    protected static function parseValidatedData(array $validated): array
    {
        $status = Status::from($validated['status']);
        $paymentStatus = PaymentStatus::from($validated['payment_status']);
        $travelers = $validated['travelers'] ?? [];
        $adults = $travelers['adults'] ?? [];
        $children = $travelers['children'] ?? [];
        $mainBookerIndex = $validated['main_booker'] ?? null;
        $mainBookerFullName = self::getMainBookerFullName($adults, $mainBookerIndex);
        $mainBooker = ['name' => $mainBookerFullName, 'index' => $mainBookerIndex];

        return [
            'status' => $status,
            'payment_status' => $paymentStatus,
            'main_booker' => $mainBooker,
            'travelers' => [
                TravelerType::Adult->value => BookingTravelerData::manyFromArray($adults),
                TravelerType::Child->value => BookingTravelerData::manyFromArray($children),
            ],
            'contact' => BookingContactData::fromArray($mainBookerFullName, $validated['contact']),
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
    protected static function findTrip(?int $tripId): Product
    {
        if (! $tripId) {
            Log::error('Booking attempt without trip ID');
            throw new ModelNotFoundException('Trip ID is required');
        }

        $trip = Product::find($tripId);

        if (! $trip) {
            Log::error('Booking attempt for non-existing product', ['trip_id' => $tripId]);
            throw new ModelNotFoundException('Trip not found');
        }

        return $trip;
    }
}
