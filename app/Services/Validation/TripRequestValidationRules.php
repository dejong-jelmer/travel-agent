<?php

namespace App\Services\Validation;

use App\Enums\TripRequest\Status;
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
            'travelers_count' => ['nullable', 'integer', 'min:1', 'max:9'],
            'departure_station' => ['nullable', 'string', 'max:100'],
            'consent_privacy' => ['accepted'],
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
