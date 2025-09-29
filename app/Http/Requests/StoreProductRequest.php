<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

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
        $maxFileSize = config('app-settings.maxFileSize');
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'between:-999999.99,999999.99'],
            'duration' => ['required', 'integer', 'min:0'],
            'featuredImage' => ['required', 'image', 'mimes:jpg,jpeg,png,gif,bmp,svg,webp', "max:{$maxFileSize}"],
            'images' => ['required'],
            'images.*' => ['required', 'image', 'mimes:jpg,jpeg,png,gif,bmp,svg,webp', "max:{$maxFileSize}"],
            'active' => ['boolean'],
            'featured' => ['boolean'],
            'published_at' => ['nullable', 'date'],
            'countries' => ['required', 'array'],
        ];
    }
}
