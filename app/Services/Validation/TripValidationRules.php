<?php

namespace App\Services\Validation;

class TripValidationRules
{
    public static function basic(array $additions = []): array
    {
        return self::mergeRules([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
        ], $additions);


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
            'published_at' => ['required', 'date'],
        ];
    }

    public static function seo(): array
    {
        return [
            'meta_title' => ['nullable', 'string', 'max:60'],
            'meta_description' => ['nullable', 'string', 'max:160'],
        ];
    }

    public static function countries(): array
    {
        return [
            'countries' => ['required', 'array'],
        ];
    }

    public static function heroImageStore(): array
    {

        return [
            'heroImage' => ['required', ...ImageValidationRules::baseImage()],
        ];
    }

    public static function heroImageUpdate(): array
    {
        return [
            'heroImage' => ['nullable', ...ImageValidationRules::baseImageOrString()],
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

    private static function mergeRules(array $base, array $additions): array
    {
        foreach ($additions as $key => $rules) {
            if (isset($base[$key])) {
                $base[$key] = array_merge(
                    $base[$key],
                    is_array($rules) ? $rules : [$rules]
                );
            } else {
                $base[$key] = is_array($rules) ? $rules : [$rules];
            }
        }
        return $base;
    }
}
