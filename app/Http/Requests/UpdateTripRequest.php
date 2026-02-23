<?php

namespace App\Http\Requests;

use App\Services\Validation\TripValidationRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateTripRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        //  Default to empty array's on null
        emptyFormRequestToArray($this, ['highlights', 'items', 'blocked_dates']);

        $this->merge([
            'slug' => Str::slug($this->slug),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge(
            TripValidationRules::basic([
                'slug' => Rule::unique('trips', 'slug')->ignore($this->trip),
            ]),
            TripValidationRules::pricing(),
            TripValidationRules::settings(),
            TripValidationRules::seo(),
            TripValidationRules::destinations(),
            TripValidationRules::heroImageUpdate(),
            TripValidationRules::imagesUpdate(),
            TripValidationRules::items(),
            TripValidationRules::practicalInfo(),
            TripValidationRules::blockedDates(),
        );
    }
}
