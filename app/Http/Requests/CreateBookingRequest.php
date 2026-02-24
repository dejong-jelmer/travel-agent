<?php

namespace App\Http\Requests;

use App\Enums\SettingKey;
use App\Http\Requests\Traits\ValidatesMainBooker;
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

    public function authorize(): bool
    {
        return true;
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->hasAny(['trip.id', 'departure_date'])) {
                return;
            }

            $trip = Trip::find($this->input('trip.id'));

            if (! $trip || empty($trip->blocked_dates)) {
                return;
            }

            $date = Carbon::parse($this->input('departure_date'));

            if ((new BlockedDateValidator)->isBlocked($trip, $date)) {
                $validator->errors()->add('departure_date', __('validation.custom.departure_date.blocked'));
            }
        });
    }

    public function rules(): array
    {
        $seasonEnd = Setting::get(SettingKey::BookingSeasonEnd);
        $departureDateRules = ['required', 'date', 'after:today'];
        if ($seasonEnd !== null) {
            $departureDateRules[] = 'before_or_equal:'.$seasonEnd;
        }

        return array_merge(
            [
                'trip.id' => ['required', Rule::exists(Trip::class, 'id')],
                // Date & confirmation
                'departure_date' => $departureDateRules,
                'has_confirmed' => ['accepted'],
                'has_accepted_conditions' => ['accepted'],
                'travelers.*.*.full_name' => ['required', 'string', 'min:3', 'max:255'],
            ],
            BookingValidationRules::contact(),
            BookingValidationRules::travelers(),
            BookingValidationRules::mainBooker(),
        );
    }
}
