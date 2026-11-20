<?php

namespace App\Services\Validation;

use App\Enums\TripRequest\Status;
use App\Models\Departure;
use App\Models\Trip;
use Illuminate\Validation\Rule;

class TripRequestValidationRules
{
    public static function notes(): array
    {
        return [
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public static function store(): array
    {
        $currentYear = (int) date('Y');

        return array_merge(static::notes(), [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email:rfc,filter', 'max:200'],
            'phone' => ['nullable', 'string', 'max:30'],
            'preferred_month' => ['nullable', 'integer', 'min:1', 'max:12'],
            'preferred_year' => ['nullable', 'integer', 'min:'.$currentYear, 'max:'.($currentYear + 2)],
            'preferred_period_note' => ['nullable', 'string', 'max:200'],
            // 7 stands for more than 6
            'travelers_count' => ['nullable', 'integer', 'min:1', 'max:7'],
            'departure_station' => ['nullable', 'string', 'max:100'],
            'consent_privacy' => ['accepted'],
            // Only a group trip is requested on a departure
            'departure_id' => ['exclude'],
            'single_room' => ['exclude'],
            'interest' => ['exclude'],
        ]);
    }

    /**
     * A group trip is requested on one of its departures, by a group of at most 6. Its date and station come with
     * the departure, so those fields are left out. Interest is set when the visitor picked a full departure.
     */
    public static function storeForDeparture(Trip $trip): array
    {
        return array_merge(static::notes(), [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email:rfc,filter', 'max:200'],
            'phone' => ['nullable', 'string', 'max:30'],
            'departure_id' => ['required', 'integer', Rule::exists(Departure::class, 'id')->where('trip_id', $trip->id)],
            'travelers_count' => ['required', 'integer', 'min:1', 'max:6'],
            'single_room' => ['boolean'],
            'interest' => ['boolean'],
            'consent_privacy' => ['accepted'],
            'preferred_month' => ['exclude'],
            'preferred_year' => ['exclude'],
            'preferred_period_note' => ['exclude'],
            'departure_station' => ['exclude'],
        ]);
    }

    public static function update(): array
    {
        return array_merge(static::notes(), [
            'status' => ['required', Rule::enum(Status::class)],
            'booking_id' => ['nullable', 'integer', 'exists:bookings,id'],
        ]);
    }
}
