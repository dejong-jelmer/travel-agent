<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Enums\Meals;

class StoreItineraryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Prepere the request for validation, default to empty array on null
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        if ($this->input('meals') === null) {
            $this->merge([
                'meals' => [],
            ]);
        }
        if ($this->input('transport') === null) {
            $this->merge([
                'transport' => [],
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
        return [
            'title' => ['required', 'string', 'max:255'],
            'location' => ['string', 'max:255'],
            'description' => ['required', 'string'],
            'accommodation' => ['nullable', 'string', 'max:255'],
            'activities' => ['nullable', 'max:255'],
            'meals' => ['nullable', 'array'],
            'meals.*' => [Rule::in(array_column(Meals::cases(), 'value'))],
            'transport' => ['array'],
            'remark' => ['nullable', 'string', 'max:255'],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,gif,bmp,svg,webp', 'max:5120'],
        ];
    }
}
