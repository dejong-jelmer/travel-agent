<?php

namespace App\Services\Validation;

class ItineraryValidationRules
{
    public static function basic(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'day_from' => ['required', 'integer', 'min:1'],
            'day_to' => ['nullable', 'integer', 'min:1', 'gt:day_from'],
            'description' => ['required', 'string'],
        ];
    }

    public static function details(): array
    {
        return [
            'accommodation' => ['nullable', 'string', 'max:255'],
            'activities' => ['array', 'max:255'],
            'remark' => ['nullable', 'string', 'max:255'],
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
