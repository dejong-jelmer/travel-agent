<?php

namespace App\Services\Validation;

use App\Enums\Trip\ItineraryType;
use Illuminate\Validation\Rule;

class ItineraryValidationRules
{
    public static function basic(): array
    {
        return [
            'type' => ['required', Rule::enum(ItineraryType::class)],
            'title' => ['required', 'string', 'max:255'],
            'day_to' => ['nullable', 'integer', 'min:1', 'gt:day_from'],
            'description' => ['required', 'string'],
        ];
    }

    public static function details(): array
    {
        return [
            'accommodation' => ['nullable', 'string', 'max:255'],
            'remark' => ['nullable', 'string', 'max:255'],
        ];
    }

    public static function imageCreate(): array
    {
        return [
            'image' => ['nullable', ...ImageValidationRules::baseImage()],
        ];
    }

    public static function imageUpdate(): array
    {
        return [
            'image' => ['nullable', ...ImageValidationRules::baseImageOrString()],
        ];
    }
}
