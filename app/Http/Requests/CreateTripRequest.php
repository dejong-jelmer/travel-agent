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
        emptyFormRequestToArray($this, ['highlights', 'transport', 'items', 'prices', 'blocked_dates']);
<<<<<<< HEAD
=======

        $this->merge([
            'slug' => Str::slug($this->slug),
        ]);
>>>>>>> b9e884b3fa401a1a668de43a24b0f92b9502b33e
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge(
<<<<<<< HEAD
            TripValidationRules::basic(),
=======
            TripValidationRules::basic([
                'slug' => Rule::unique('trips', 'slug'),
            ]),
>>>>>>> b9e884b3fa401a1a668de43a24b0f92b9502b33e
            TripValidationRules::prices(),
            TripValidationRules::settings(),
            TripValidationRules::seo(),
            TripValidationRules::destinations(),
            TripValidationRules::transport(),
            TripValidationRules::heroImageStore(),
            TripValidationRules::imagesStore(),
            TripValidationRules::items(),
            TripValidationRules::practicalInfo(),
            TripValidationRules::blockedDates(),
        );
    }
}
