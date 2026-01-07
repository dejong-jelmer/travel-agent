<?php

namespace App\Http\Requests;

use App\Services\Validation\ItineraryValidationRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreateItineraryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge(
            ItineraryValidationRules::basic(),
            ItineraryValidationRules::details(),
            ItineraryValidationRules::options(),
            ItineraryValidationRules::imageCreate(),
        );
    }
}
