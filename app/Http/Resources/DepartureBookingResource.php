<?php

namespace App\Http\Resources;

use App\Models\Booking;
use App\Models\BookingContact;
use App\Models\BookingTraveler;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A booking as a row of the participant list of a departure. Expects the travelers and the contact to be loaded.
 *
 * @mixin Booking
 */
class DepartureBookingResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var BookingContact|null $contact An anonymized booking has none */
        $contact = $this->getRelation('contact');

        $travelers = [];
        $mainBooker = null;

        /** @var BookingTraveler $traveler */
        foreach ($this->travelers as $traveler) {
            $name = $traveler->getAttribute('full_name');

            if ($traveler->id === $this->main_booker_id) {
                $mainBooker = $name;
            }

            $travelers[] = [
                'id' => $traveler->id,
                'full_name' => $name,
                'type' => $traveler->type->value,
                'birthdate' => $traveler->getAttribute('birthdate_formatted'),
                'special_requests' => $traveler->special_requests,
            ];
        }

        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'payment_status' => $this->payment_status->value,
            'payment_status_label' => $this->payment_status->label(),
            'occupies_seat' => $this->status->occupiesSeat(),
            'counts_towards_minimum' => $this->status->countsTowardsMinimum(),
            'participants' => ($this->total_adults ?? 0) + ($this->total_children ?? 0),
            'single_rooms' => $this->single_rooms,
            // In cents
            'display_price' => $this->getAttribute('display_price'),
            'main_booker' => $mainBooker,
            'email' => $contact?->email,
            'phone' => $contact?->phone,
            'travelers' => $travelers,
        ];
    }
}
