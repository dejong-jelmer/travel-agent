<?php

namespace App\Http\Requests;

use App\Services\Validation\TripRequestValidationRules;
use Illuminate\Foundation\Http\FormRequest;

class CreateTripRequestRequest extends FormRequest
{
    public function rules(): array
    {
        return TripRequestValidationRules::store();
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
        ];
    }
}
