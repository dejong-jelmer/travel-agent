<?php

namespace App\Rules;

use App\Models\Itinerary;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NoOverlappingItineraryDays implements ValidationRule
{
    public function __construct(
        private ?int $tripId,
        private ?int $excludeId = null
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (is_null($this->tripId)) {
            return;
        }

        $dayFrom = (int) $value;
        $dayTo = (int) request('day_to') ?: $dayFrom;

        $overlap = Itinerary::where('trip_id', $this->tripId)
            ->when($this->excludeId, fn ($q) => $q->where('id', '!=', $this->excludeId))
            ->where(function ($query) use ($dayFrom, $dayTo) {
                $query->whereBetween('day_from', [$dayFrom, $dayTo])
                    ->orWhereBetween('day_to', [$dayFrom, $dayTo])
                    ->orWhere(function ($q) use ($dayFrom, $dayTo) {
                        $q->where('day_from', '<=', $dayFrom)
                            ->where('day_to', '>=', $dayTo);
                    });
            })
            ->exists();

        if ($overlap) {
            $fail(__('validation.custom.itinerary.days.overlap'));
        }
    }
}
