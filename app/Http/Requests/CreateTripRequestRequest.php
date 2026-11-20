<?php

namespace App\Http\Requests;

use App\Enums\Trip\TripFormat;
use App\Models\Departure;
use App\Models\Trip;
use App\Services\Validation\TripRequestValidationRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class CreateTripRequestRequest extends FormRequest
{
    public function rules(): array
    {
        return $this->isForGroupTrip()
            ? TripRequestValidationRules::storeForDeparture($this->trip())
            : TripRequestValidationRules::store();
    }

    /**
     * @return array<int, \Closure>
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($this->isForGroupTrip() && ! $validator->errors()->hasAny(['departure_id', 'travelers_count'])) {
                    $this->validateDeparture($validator);
                }
            },
        ];
    }

    /**
     * The departure has to be open for requests and have room for the group. A full departure only takes interest
     * in a place that comes free. A request reserves nothing, so this is checked without a lock.
     */
    private function validateDeparture(Validator $validator): void
    {
        $departure = Departure::withParticipantCounts()->findOrFail($this->input('departure_id'));
        $seatsLeft = $departure->availableSeats();
        $travelers = (int) $this->input('travelers_count');

        if (! $departure->acceptsRequests($this->trip()->min_advance_days)) {
            $validator->errors()->add('departure_id', __('trip_request.validation.departure_closed'));
        } elseif ($seatsLeft === 0 && ! $this->boolean('interest')) {
            $validator->errors()->add('departure_id', __('trip_request.validation.departure_full'));
        } elseif ($seatsLeft > 0 && $seatsLeft < $travelers) {
            $validator->errors()->add('travelers_count', trans_choice('trip_request.validation.not_enough_seats', $seatsLeft, ['count' => $seatsLeft]));
        }
    }

    private function trip(): Trip
    {
        /** @var Trip $trip */
        $trip = $this->route('trip');

        return $trip;
    }

    private function isForGroupTrip(): bool
    {
        return $this->trip()->format === TripFormat::Group;
    }

    public function messages(): array
    {
        return [
            'name.required' => __('trip_request.validation.name_required'),
            'name.max' => __('trip_request.validation.name_max'),
            'email.required' => __('trip_request.validation.email_required'),
            'email.email' => __('trip_request.validation.email_email'),
            'phone.max' => __('trip_request.validation.phone_max'),
            'preferred_period_note.max' => __('trip_request.validation.preferred_period_note_max'),
            'notes.max' => __('trip_request.validation.notes_max'),
            'consent_privacy.accepted' => __('trip_request.validation.consent_privacy_accepted'),
            'departure_id.required' => __('trip_request.validation.departure_required'),
            'departure_id.exists' => __('trip_request.validation.departure_closed'),
            'travelers_count.required' => __('trip_request.validation.travelers_count_required'),
            'travelers_count.max' => __('trip_request.validation.travelers_count_max'),
        ];
    }
}
