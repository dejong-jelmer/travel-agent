<?php

namespace App\Services\Validation;

class TripValidationRules
{
    public static function basic(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
        ];
    }

    public static function pricing(): array
    {
        return [
            'price' => ['required', 'numeric', 'between:-999999.99,999999.99'],
            'duration' => ['required', 'integer', 'min:0'],
        ];
    }

    public static function settings(): array
    {
        return [
            'active' => ['boolean'],
            'featured' => ['boolean'],
            'published_at' => ['nullable', 'date'],
        ];
    }

    public static function countries(): array
    {
        return [
            'countries' => ['required', 'array'],
        ];
    }

    public static function featuredImageStore(): array
    {

        return [
            'featuredImage' => ['required', ...ImageValidationRules::baseImage()],
        ];
    }

    public static function featuredImageUpdate(): array
    {
        return [
            'featuredImage' => ['nullable', ...ImageValidationRules::baseImageOrString()],
        ];
    }

    public static function imagesStore(): array
    {
        return [
            'images' => ['required', 'array'],
            'images.*' => ['required', ...ImageValidationRules::baseImage()],
        ];
    }

    public static function imagesUpdate(): array
    {
        return [
            'images' => ['nullable', 'array'],
            'images.*' => ImageValidationRules::baseImageOrString(),
        ];
    }
}
