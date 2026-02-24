<?php

namespace App\Http\Requests;

use App\Services\Validation\TripValidationRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class CreateTripRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            foreach ($this->input('blocked_dates.dates', []) as $index => $entry) {
                if (! is_array($entry)) {
                    continue;
                }

                $start = $entry['start'] ?? null;
                $end = $entry['end'] ?? null;

                if (! $start || ! $end) {
                    continue;
                }

                if ($validator->errors()->hasAny([
                    "blocked_dates.dates.{$index}.start",
                    "blocked_dates.dates.{$index}.end",
                ])) {
                    continue;
                }

                if ($start > $end) {
                    $validator->errors()->add(
                        "blocked_dates.dates.{$index}.end",
                        __('validation.custom.date_range_end')
                    );
                }
            }
        });
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
                'slug' => Rule::unique('trips', 'slug'),
            ]),
            TripValidationRules::pricing(),
            TripValidationRules::settings(),
            TripValidationRules::seo(),
            TripValidationRules::destinations(),
            TripValidationRules::heroImageStore(),
            TripValidationRules::imagesStore(),
            TripValidationRules::items(),
            TripValidationRules::practicalInfo(),
            TripValidationRules::blockedDates(),
        );
    }
}
