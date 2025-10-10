<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ValidatesMainBooker;
use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBookingRequest extends FormRequest
{
    use ValidatesMainBooker;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'trip.id' => ['required', Rule::exists(Product::class, 'id')],
            // Selectie datum & bevestiging
            'departure_date' => ['required', 'date', 'after:today'],
            'is_confirmed' => ['accepted'],
            'conditions_accepted' => ['accepted'],

            // Contactgegevens
            'contact.street' => ['required', 'string', 'min:3', 'max:255'],
            'contact.house_number' => ['required', 'regex:/^\d+[a-zA-Z0-9\-]*$/'],
            'contact.addition' => ['nullable', 'string', 'max:10', 'regex:/^[0-9A-Za-z\s\-]+$/i'],
            'contact.postal_code' => ['required', 'regex:/^(?:\d{4}\s?[A-Z]{2}|[1-9]\d{3})$/i'],
            'contact.city' => ['required', 'string', 'min:3', 'max:255'],
            'contact.email' => ['required', 'email:rfc,dns'],
            'contact.phone' => ['required', 'string', 'phone:NL,BE'],

            // Reizigers volwassenen
            'travelers.*.*.first_name' => ['required', 'string', 'min:3', 'max:255'],
            'travelers.*.*.last_name' => ['required', 'string', 'min:3', 'max:255'],
            'travelers.*.*.nationality' => ['required', 'string', 'min:2', 'max:255'],
            'travelers.*.*.full_name' => ['required', 'string', 'min:3', 'max:255'],
            'travelers.adults.*.birthdate' => [
                'required',
                'date_format:d-m-Y',
                'before:'.now()->subYears(12)->format('d-m-Y'),
                'after:'.now()->subYears(125)->format('d-m-Y'),
            ],
            'travelers.children.*.birthdate' => [
                'required',
                'date_format:d-m-Y',
                'after_or_equal:'.now()->subYears(12)->format('d-m-Y'),
                'before:'.now()->format('d-m-Y'),
            ],
            // Main booker
            'main_booker' => ['required', 'integer'],
        ];
    }
}
