<?php

namespace App\Services\Validation;

class ProductValidationRules
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
        $maxFileSize = config('app-settings.maxFileSize');
        $mimes = config('app-settings.mimes');

        return [
            'featuredImage' => ['required', 'image', "mimes:{$mimes}", "max:{$maxFileSize}"],
        ];
    }

    public static function featuredImageUpdate(): array
    {
        $maxFileSize = config('app-settings.maxFileSize');
        $mimes = config('app-settings.mimes');

        return [
            'featuredImage' => ['required', 'image', "mimes:{$mimes}", "max:{$maxFileSize}"],
        ];
    }

    public static function imagesStore(): array
    {
        $maxFileSize = config('app-settings.maxFileSize');
        $mimes = config('app-settings.mimes');

        return [
            'images' => ['required'],
            'images.*' => ['required', 'image', "mimes:{$mimes}", "max:{$maxFileSize}"],
        ];
    }

    public static function imagesUpdate(): array
    {
        $maxFileSize = config('app-settings.maxFileSize');
        $mimes = config('app-settings.mimes');

        return [
            'images' => ['required'],
            'images.*' => ['required', 'image', "mimes:{$mimes}", "max:{$maxFileSize}"],
        ];
    }
}
