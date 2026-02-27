<?php

namespace App\Http\Requests;

use App\Rules\NoOverlappingItineraryDays;
use App\Services\Validation\ItineraryValidationRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateItineraryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    /**
     * Prepare the request for validation
     */
    protected function prepareForValidation(): void
    {
        //  Default to empty array's on null
        emptyFormRequestToArray($this, 'activities');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge(
            [
                'trip_id' => ['required', 'exists:trips,id'],
                'day_from' => [
                    'required',
                    'integer',
                    'min:1',
                    new NoOverlappingItineraryDays(
                        tripId: $this->trip_id,
                        excludeId: $this->route('itinerary')?->id // null bij create
                    ),
                ],
            ],
            ItineraryValidationRules::basic(),
            ItineraryValidationRules::details(),
            ItineraryValidationRules::imageUpdate(),
        );
    }
}
