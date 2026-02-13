<?php

namespace App\Http\Requests;

use App\Enums\Destination\TravelInfo;
use App\Rules\EnumKeys;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreDestinationRequest extends FormRequest
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
    protected function prepareForValidation(): void
    {
        if (is_array($this->travel_info) && empty(array_filter($this->travel_info))) {
            $this->merge(['travel_info' => null]);
        }
    }

    public function rules(): array
    {
        return [
            'country_code' => 'required|string|size:2|exists:countries,code',
            'region' => 'nullable|string|max:255',
            'travel_info' => ['nullable', new EnumKeys(TravelInfo::class)],
            'travel_info.*' => [
                'nullable',
                'string',
                'max:10000',

            ],
        ];
    }
}
