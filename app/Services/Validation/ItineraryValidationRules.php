<?php

namespace App\Services\Validation;

use App\Enums\Meal;
use App\Enums\Transport;
use Illuminate\Validation\Rule;

class ItineraryValidationRules
{
    public static function basic(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'location' => ['string', 'max:255'],
            'description' => ['required', 'string'],
        ];
    }

    public static function details(): array
    {
        return [
            'accommodation' => ['nullable', 'string', 'max:255'],
            'activities' => ['nullable', 'max:255'],
            'remark' => ['nullable', 'string', 'max:255'],
        ];
    }

    public static function options(): array
    {
        return [
            'meals' => ['nullable', 'array'],
            'meals.*' => [
                'required',
                'string',
                Rule::in(Meal::values()),
            ],
            'transport' => ['nullable', 'array'],
            'transport.*' => [
                'required',
                'string',
                Rule::in(Transport::values()),
            ],
        ];
    }

    public static function imageCreate(): array
    {
        return [
            'image' => ['required', ...ImageValidationRules::baseImage()],
        ];
    }

    public static function imageUpdate(): array
    {
        return [
            'image' => ['nullable', ...ImageValidationRules::baseImageOrString()],
        ];
    }
}
