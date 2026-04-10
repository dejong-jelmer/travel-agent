<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTripRequestRequest extends FormRequest
{
    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $currentYear = (int) date('Y');

        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email:rfc,filter', 'max:200'],
            'phone' => ['nullable', 'string', 'max:30'],
            'preferred_month' => ['nullable', 'integer', 'min:1', 'max:12'],
            'preferred_year' => ['nullable', 'integer', 'min:'.$currentYear, 'max:'.($currentYear + 2)],
            'preferred_period_note' => ['nullable', 'string', 'max:200'],
            'travelers_count' => ['nullable', 'integer', 'min:1', 'max:9'],
            'departure_station' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'consent_privacy' => ['accepted'],
        ];
    }

    /**
     * @return array<string, string>
     */
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
        ];
    }
}
