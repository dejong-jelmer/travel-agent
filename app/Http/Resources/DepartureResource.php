<?php

namespace App\Http\Resources;

use App\Models\Departure;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A departure as the admin pages show it: its own values, its participant counts and what follows from them.
 *
 * Load the departures with the withParticipantCounts() scope, or every departure runs its own count queries.
 *
 * @mixin Departure
 */
class DepartureResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $status = $this->status();
        $awaitsDecision = ! $this->isGuaranteed() && ! $this->isCancelled() && ! $this->isCompleted();

        return [
            'id' => $this->id,
            'trip_id' => $this->trip_id,
            'departs_on' => $this->departs_on->toDateString(),
            'returns_on' => $this->returns_on->toDateString(),
            'duration_in_days' => $this->durationInDays(),
            'min_participants' => $this->min_participants,
            'max_participants' => $this->max_participants,
            // In cents, null when the price of the trip applies
            'price_per_person' => $this->price_per_person,
            'single_supplement' => $this->single_supplement,
            'decision_date' => $this->decision_date->toDateString(),
            'latest_decision_date' => $this->latestDecisionDate()->toDateString(),
            'guaranteed_at' => $this->guaranteed_at?->toIso8601String(),
            'cancelled_at' => $this->cancelled_at?->toIso8601String(),
            'cancellation_reason' => $this->cancellation_reason,
            'status' => ['value' => $status->value, 'label' => $status->label()],
            'is_guaranteed' => $this->isGuaranteed(),
            'occupied_seats' => $this->occupiedSeats(),
            'confirmed_participants' => $this->confirmedParticipants(),
            'available_seats' => $this->availableSeats(),
            'has_reached_minimum' => $this->hasReachedMinimum(),
            // The minimum is reached and the admin has not decided yet
            'awaits_guarantee' => $awaitsDecision && $this->hasReachedMinimum(),
            // The decision date has passed without a decision
            'decision_overdue' => $awaitsDecision && $this->decision_date->lt(today()),
            'can_be_edited' => $this->canBeEdited(),
            'can_be_guaranteed' => $awaitsDecision,
            'can_be_cancelled' => $this->canBeCancelled(),
        ];
    }
}
