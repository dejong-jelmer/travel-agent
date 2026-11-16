<?php

namespace App\Http\Resources;

use App\Models\TripRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * An open request as a row on the page of its departure.
 *
 * @mixin TripRequest
 */
class DepartureRequestResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'travelers' => $this->travelersLabel(),
            'single_room' => $this->single_room,
            'interest_only' => $this->interest_only,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'created_at_formatted' => $this->getAttribute('created_at_formatted'),
        ];
    }
}
