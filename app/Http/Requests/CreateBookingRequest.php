<?php

namespace App\Http\Requests;

use App\Enums\SettingKey;
use App\Enums\Trip\TripFormat;
use App\Http\Requests\Traits\ValidatesMainBooker;
use App\Models\Departure;
use App\Models\Setting;
use App\Models\Trip;
use App\Services\Validation\BlockedDateValidator;
use App\Services\Validation\BookingValidationRules;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class CreateBookingRequest extends FormRequest
{
    use ValidatesMainBooker;

    private const MIN_TRAVELERS = 1;

    private const MAX_TRAVELERS = 6;

    public function authorize(): bool
    {
        return true;
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($this->isForGroupTrip()) {
                $this->validateDeparture($validator);

                return;
            }

            if ($validator->errors()->hasAny(['trip.id', 'departure_date'])) {
                return;
            }

            $trip = $this->trip();

            if (! $trip || empty($trip->blocked_dates)) {
                return;
            }

            $date = Carbon::parse($this->input('departure_date'));

            if ((new BlockedDateValidator)->isBlocked($trip, $date)) {
                $validator->errors()->add('departure_date', __('validation.custom.departure_date.blocked'));
            }
        });
    }

    /**
     * A first check that the departure has room for the travelers, for a clear message in the form. BookingService
     * checks again with the departure locked, which is what keeps two bookings from taking the same places.
     */
    private function validateDeparture(Validator $validator): void
    {
        if ($validator->errors()->hasAny(['trip.id', 'departure_id', 'travelers'])) {
            return;
        }

        $travelers = count($this->input('travelers.adults', [])) + count($this->input('travelers.children', []));
        $refusal = Departure::findOrFail($this->input('departure_id'))->refusalToBook($travelers);

        if ($refusal !== null) {
            $validator->errors()->add('departure_id', $refusal);
        }
    }

    private function trip(): ?Trip
    {
        return Trip::find($this->input('trip.id'));
    }

    private function isForGroupTrip(): bool
    {
        return $this->trip()?->format === TripFormat::Group;
    }

    /**
     * The date rules of an individual trip, where the customer picks the dates, or the departure rule of a group
     * trip, where the dates come from the departure and submitted dates are left out.
     *
     * @return array<string, array<int, mixed>>
     */
    private function dateRules(): array
    {
        if ($this->isForGroupTrip()) {
            return [
                'departure_id' => [
                    'required',
                    'integer',
                    Rule::exists(Departure::class, 'id')->where('trip_id', $this->input('trip.id')),
                ],
                'departure_date' => ['exclude'],
                'return_date' => ['exclude'],
            ];
        }

        $seasonEnd = Setting::get(SettingKey::BookingSeasonEnd);
        $departureDateRules = ['required', 'date', 'after:today'];
        if ($seasonEnd !== null) {
            $departureDateRules[] = 'before_or_equal:'.$seasonEnd;
        }

        return [
            'departure_id' => ['prohibited'],
            'departure_date' => $departureDateRules,
            'return_date' => ['nullable', 'date', 'after:departure_date'],
        ];
    }

    public function messages(): array
    {
        return [
            'departure_id.required' => __('departure.booking.required'),
        ];
    }

    public function rules(): array
    {
        return array_merge(
            $this->dateRules(),
            [
                'trip.id' => ['required', Rule::exists(Trip::class, 'id')],
                'has_confirmed' => ['accepted'],
                'has_accepted_conditions' => ['accepted'],
                'travelers' => [
                    'array',
                    function (string $attribute, mixed $value, \Closure $fail) {
                        $total = count($value['adults'] ?? []) + count($value['children'] ?? []);
                        if ($total < self::MIN_TRAVELERS || $total > self::MAX_TRAVELERS) {
                            $fail(__('validation.between.numeric', ['attribute' => $attribute, 'min' => self::MIN_TRAVELERS, 'max' => self::MAX_TRAVELERS]));
                        }
                    },
                ],
                'travelers.*.*.full_name' => ['required', 'string', 'min:3', 'max:255'],
            ],
            BookingValidationRules::contact(),
            BookingValidationRules::travelers(),
            BookingValidationRules::mainBooker(),
            BookingValidationRules::costItems(),
        );
    }
}
