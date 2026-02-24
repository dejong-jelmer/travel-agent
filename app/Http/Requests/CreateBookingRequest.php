<?php

namespace App\Http\Requests;

use App\Enums\SettingKey;
use App\Http\Requests\Traits\ValidatesMainBooker;
use App\Models\Setting;
use App\Models\Trip;
use App\Services\Validation\BookingValidationRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateBookingRequest extends FormRequest
{
    use ValidatesMainBooker;

    public function authorize(): bool
    {
        return true;
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
                // Selectie datum & bevestiging
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
