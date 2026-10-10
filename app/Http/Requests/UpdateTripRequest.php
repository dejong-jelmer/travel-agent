<?php

namespace App\Http\Requests;

use App\Http\Requests\Traits\ValidatesBlockedDateRanges;
use App\Models\Trip;
use App\Services\TripContentParser;
use App\Services\Validation\TripValidationRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateTripRequest extends FormRequest
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

        // Only the sections after the first of the submitted description can get a photo: keys of renamed or removed
        // headings are ignored, so they are cleaned up on save, as are the sections set to no photo
        $sectionKeys = array_column(
            app(TripContentParser::class)->storySections(is_string($this->input('description')) ? $this->input('description') : null),
            'key'
        );
        $this->merge([
            'section_images' => collect($this->input('section_images'))
                ->filter(fn ($imageId, $key) => filled($imageId) && in_array((string) $key, $sectionKeys, true))
                ->all(),
        ]);

        // Cast FormData string to integer
        if ($this->filled('min_advance_days')) {
            $this->merge(['min_advance_days' => (int) $this->input('min_advance_days')]);
        }

        // Normalize blocked_dates sub-fields: FormData omits empty arrays,
        // so explicitly default dates and weekdays to [] when absent.
        $blockedDates = $this->input('blocked_dates');
        if (is_array($blockedDates)) {
            $this->merge([
                'blocked_dates' => [
                    'dates' => array_values($blockedDates['dates'] ?? []),
                    'weekdays' => $blockedDates['weekdays'] ?? [],
                ],
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var Trip $trip */
        $trip = $this->route('trip');

        return array_merge(
            TripValidationRules::basic(),
            TripValidationRules::journeySection($this->input('description')),
            TripValidationRules::sectionImages($trip),
            TripValidationRules::keyFacts(),
            TripValidationRules::prices(),
            TripValidationRules::settings(),
            TripValidationRules::seo(),
            TripValidationRules::destinations(),
            TripValidationRules::transport(),
            TripValidationRules::heroImageUpdate(),
            TripValidationRules::heroFocus(),
            TripValidationRules::imagesUpdate(),
            TripValidationRules::items(),
            TripValidationRules::practicalInfo(),
            TripValidationRules::blockedDates(),
        );
    }
}
