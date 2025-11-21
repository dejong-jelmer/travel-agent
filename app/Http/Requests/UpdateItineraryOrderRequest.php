<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateItineraryOrderRequest extends FormRequest
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
        $trip = $this->route('trip');

        return [
            'itineraries' => 'required|array',
            'itineraries.*.id' => 'exists:itineraries,id',
            'itineraries.*.order' => "integer|between:0,{$trip->itineraries()->count()}",
        ];
    }
}
