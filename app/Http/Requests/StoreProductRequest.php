<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'duration' => ['required', 'integer', 'min:0'],
            // 'image' => ['required', 'image'],
            'images' => ['array'],
            'active' => ['boolean'],
            'featured' => ['boolean'],
            'published_at' => ['nullable', 'date'],
            // 'category_id' => ['required', 'exists:categories,id'],
            // 'country_id' => ['required', 'exists:countries,id'],
        ];
    }
}
