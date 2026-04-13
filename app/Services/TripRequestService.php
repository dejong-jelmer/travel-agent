<?php

namespace App\Services;

use App\Enums\TripRequest\Status;
use App\Models\Trip;
use App\Models\TripRequest;

class TripRequestService
{
    public function store(array $validated, Trip $trip): TripRequest
    {
        $preferredMonth = null;
        if (! empty($validated['preferred_year']) && ! empty($validated['preferred_month'])) {
            $preferredMonth = $validated['preferred_year'].'-'.str_pad($validated['preferred_month'], 2, '0', STR_PAD_LEFT);
        }

        return TripRequest::create([
            'trip_id' => $trip->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'preferred_month' => $preferredMonth,
            'preferred_period_note' => $validated['preferred_period_note'] ?? null,
            'travelers_count' => $validated['travelers_count'] ?? null,
            'departure_station' => $validated['departure_station'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => Status::New,
            'consent_privacy' => ! empty($validated['consent_privacy']),
            'consent_privacy_at' => ! empty($validated['consent_privacy']) ? now() : null,
        ]);
    }
}
