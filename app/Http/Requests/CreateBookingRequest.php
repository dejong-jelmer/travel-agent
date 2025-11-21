<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ValidatesMainBooker;
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
        return array_merge(
            [
                'trip.id' => ['required', Rule::exists(Trip::class, 'id')],
                // Selectie datum & bevestiging
                'departure_date' => ['required', 'date', 'after:today'],
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
