<?php

namespace App\Services\Validation;

use App\Enums\Meals;
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
            'meals.*' => [Rule::in(array_column(Meals::cases(), 'value'))],
            'transport' => ['array'],
        ];
    }

    public static function imageCreate(): array
    {
        return [
            'image' => ['required', ...self::baseImage()],
        ];
    }

    public static function imageUpdate(): array
    {
        return [
            'image' => ['nullable', ...self::baseImage()],
        ];
    }

    private static function baseImage(): array
    {
        $maxFileSize = config('app-settings.maxFileSize');
        $mimes = config('app-settings.mimes');

        return ['image', "mimes:{$mimes}", "max:{$maxFileSize}"];
    }
}
