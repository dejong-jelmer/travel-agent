<?php

namespace App\Http\Requests;

use App\Http\Requests\Traits\ValidatesBlockedDateRanges;
use App\Services\Validation\TripValidationRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreateTripRequest extends FormRequest
{
    use ValidatesBlockedDateRanges;

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
        emptyFormRequestToArray($this, ['highlights', 'key_facts', 'transport', 'items', 'prices', 'blocked_dates']);

        // Ignore the empty trailing row of the key facts input (its preselected icon does not count)
        dropBlankListItems($this, 'key_facts', ignoredKeys: ['icon']);

        // Null out rich text sections left empty in the editor
        nullifyEmptyHtml($this, 'practical_info');

        // Cast FormData string to integer
        if ($this->filled('min_advance_days')) {
            $this->merge(['min_advance_days' => (int) $this->input('min_advance_days')]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge(
            TripValidationRules::basic(),
            TripValidationRules::journeySection($this->input('description')),
            TripValidationRules::keyFacts(),
            TripValidationRules::prices(),
            TripValidationRules::settings(),
            TripValidationRules::seo(),
            TripValidationRules::destinations(),
            TripValidationRules::transport(),
            TripValidationRules::heroImageStore(),
            TripValidationRules::heroFocus(),
            TripValidationRules::imagesStore(),
            TripValidationRules::items(),
            TripValidationRules::practicalInfo(),
            TripValidationRules::blockedDates(),
        );
    }
}
