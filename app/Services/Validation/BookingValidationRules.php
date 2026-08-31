<?php

namespace App\Services\Validation;

use App\Enums\Booking\CostCategory;
use Illuminate\Validation\Rule;

class BookingValidationRules
{
    public static function contact(): array
    {
        return [
            'contact.street' => ['required', 'string', 'min:3', 'max:255'],
            'contact.house_number' => ['required', 'regex:/^\d+[a-zA-Z0-9\-]*$/'],
            'contact.addition' => ['nullable', 'string', 'max:10', 'regex:/^[0-9A-Za-z\s\-]+$/i'],
            'contact.postal_code' => ['required', 'regex:/^(?:\d{4}\s?[A-Z]{2}|[1-9]\d{3})$/i'],
            'contact.city' => ['required', 'string', 'min:3', 'max:255'],
            'contact.email' => ['required', 'email:rfc,filter'],
            'contact.phone' => ['required', 'string', 'min:10', 'max:20'],
        ];
    }

    public static function travelers(): array
    {
        return [
            'travelers.*.*.first_name' => ['required', 'string', 'min:2', 'max:255'],
            'travelers.*.*.last_name' => ['required', 'string', 'min:2', 'max:255'],
            'travelers.*.*.nationality' => ['required', 'string', 'min:2', 'max:255'],
            'travelers.*.*.special_requests' => ['nullable', 'string', 'max:1000'],
            'travelers.*.*.special_requests_consent' => [
                function (string $attribute, mixed $value, \Closure $fail) {
                    $specialRequestsKey = str_replace('special_requests_consent', 'special_requests', $attribute);
                    $specialRequests = data_get(request()->all(), $specialRequestsKey);

                    if (filled($specialRequests) && ! $value) {
                        $fail(__('validation.custom.special_requests_consent_required'));
                    }
                },
            ],
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
        ];
    }

    public static function mainBooker(): array
    {
        return [
            'main_booker' => ['required', 'integer'],
        ];
    }

    public static function costItems(): array
    {
        return [
            'cost_items' => ['required', 'array', 'min:1'],
            'cost_items.*.category' => ['required', Rule::enum(CostCategory::class)],
            'cost_items.*.label' => ['required', 'string', 'max:255'],
            'cost_items.*.amount_per_person' => ['required', 'integer', 'min:1'],
            'cost_items.*.quantity' => ['required', 'integer', 'between:1,20'],
            'margin_in_percentage' => ['nullable', 'boolean'],
            'margin_percentage' => ['required_unless:margin_in_percentage,false', 'nullable', 'numeric', 'between:0,95'],
            'margin_amount' => ['required_if:margin_in_percentage,false', 'nullable', 'integer', 'min:0'],
            'fee_per_person' => ['required_if:margin_in_percentage,false', 'nullable', 'integer', 'min:0'],
            'final_price' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
