<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ValidatesMainBooker;
use App\Models\Product;
use App\Services\Validation\BookingValidationRules;
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
        return array_merge(
            [
                'trip.id' => ['required', Rule::exists(Product::class, 'id')],
                // Selectie datum & bevestiging
                'departure_date' => ['required', 'date', 'after:today'],
                'is_confirmed' => ['accepted'],
                'conditions_accepted' => ['accepted'],
                'travelers.*.*.full_name' => ['required', 'string', 'min:3', 'max:255']
            ],
            BookingValidationRules::contact(),
            BookingValidationRules::travelers(),
            BookingValidationRules::mainBooker(),
        );
    }
}
