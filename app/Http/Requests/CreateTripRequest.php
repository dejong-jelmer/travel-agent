<?php

namespace App\Http\Requests;

use App\Services\Validation\TripValidationRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreateTripRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
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
            TripValidationRules::pricing(),
            TripValidationRules::settings(),
            TripValidationRules::countries(),
            TripValidationRules::featuredImageStore(),
            TripValidationRules::imagesStore(),
        );
    }
}
