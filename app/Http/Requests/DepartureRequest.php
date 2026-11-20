<?php

namespace App\Http\Requests;

use App\Enums\Trip\TripFormat;
use App\Models\Departure;
use App\Models\Trip;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;

/**
 * Validates a departure for both creating it on a trip and updating it.
 */
class DepartureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            // A new departure has yet to leave; an existing one keeps the dates it has
            'departs_on' => ['required', 'date', ...($this->departure() ? [] : ['after:today'])],
            'returns_on' => ['required', 'date', 'after_or_equal:departs_on'],
            'min_participants' => ['required', 'integer', 'min:1', 'max:500'],
            'max_participants' => ['required', 'integer', 'gte:min_participants', 'max:500'],
            // In euros. Empty falls back on the price of the trip.
            'price_per_person' => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'single_supplement' => ['nullable', 'numeric', 'min:0', 'max:100000'],
            // Empty becomes the last day the law allows
            'decision_date' => ['nullable', 'date'],
            'guaranteed' => ['boolean'],
        ];
    }

    /**
     * @return array<int, \Closure>
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $this->validateTrip($validator);
                $this->validateDecisionDate($validator);
                $this->validateExistingDeparture($validator);
            },
        ];
    }

    private function validateTrip(Validator $validator): void
    {
        $trip = $this->route('trip');

        if ($trip instanceof Trip && $trip->format !== TripFormat::Group) {
            $validator->errors()->add('departs_on', __('departure.validation.not_a_group_trip'));
        }
    }

    /**
     * The decision may not come later than the law allows for the length of the trip. When that day has already
     * passed, the minimum can no longer be relied on, so a new departure has to be guaranteed from the start.
     */
    private function validateDecisionDate(Validator $validator): void
    {
        $latest = Departure::latestDecisionDateFor(Carbon::parse($this->input('departs_on')), Carbon::parse($this->input('returns_on')));

        if ($this->filled('decision_date') && Carbon::parse($this->input('decision_date'))->startOfDay()->gt($latest)) {
            $validator->errors()->add('decision_date', __('departure.validation.decision_date_too_late', [
                'date' => $latest->format('d-m-Y'),
            ]));

            return;
        }

        if (! $this->departure() && $latest->lt(today()) && ! $this->boolean('guaranteed')) {
            $validator->errors()->add('decision_date', __('departure.validation.deadline_passed', [
                'date' => $latest->format('d-m-Y'),
            ]));
        }
    }

    private function validateExistingDeparture(Validator $validator): void
    {
        $departure = $this->departure();

        if (! $departure) {
            return;
        }

        if (! $departure->canBeEdited()) {
            $validator->errors()->add('departs_on', __('departure.validation.not_editable'));

            return;
        }

        if ((int) $this->input('max_participants') < $departure->occupiedSeats()) {
            $validator->errors()->add('max_participants', __('departure.validation.max_below_occupied', [
                'occupied' => $departure->occupiedSeats(),
            ]));
        }
    }

    private function departure(): ?Departure
    {
        $departure = $this->route('departure');

        return $departure instanceof Departure ? $departure : null;
    }
}
