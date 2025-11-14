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

        return [
            'featuredImage' => ['required', ...self::baseImage()],
        ];
    }

    public static function featuredImageUpdate(): array
    {
        return [
            'featuredImage' => ['nullable', ...self::baseImageOrString()],
        ];
    }

    public static function imagesStore(): array
    {
        return [
            'images' => ['required', 'array'],
            'images.*' => ['required', ...self::baseImage()],
        ];
    }

    public static function imagesUpdate(): array
    {
        return [
            'images' => ['nullable', 'array'],
            'images.*' => self::baseImageOrString(),
        ];
    }

    private static function baseImage(): array
    {
        $maxFileSize = config('app-settings.maxFileSize');
        $mimes = config('app-settings.mimes');

        return ['image', "mimes:{$mimes}", "max:{$maxFileSize}"];
    }

    private static function baseImageOrString(): array
    {
        $maxFileSize = config('app-settings.maxFileSize');
        $mimes = config('app-settings.mimes');

        return [
            function ($attribute, $value, $fail) use ($maxFileSize, $mimes) {
                // Accept string paths for existing images
                if (is_string($value)) {
                    // Validate it's a valid image filename
                    $extension = strtolower(pathinfo($value, PATHINFO_EXTENSION));
                    $allowedExtensions = explode(',', $mimes);
                    if (! in_array($extension, $allowedExtensions)) {
                        $fail("The {$attribute} must be a valid image file.");
                    }

                    return;
                }

                // Accept UploadedFile for new uploads
                if ($value instanceof \Illuminate\Http\UploadedFile) {
                    // Validate as image
                    if (! str_starts_with($value->getMimeType(), 'image/')) {
                        $fail("The {$attribute} must be an image.");
                    }

                    // Validate mime type
                    $mimeTypes = array_map(fn ($ext) => 'image/'.$ext, explode(',', $mimes));
                    if (! in_array($value->getMimeType(), $mimeTypes)) {
                        $fail("The {$attribute} must be a file of type: {$mimes}.");
                    }

                    // Validate file size (in KB)
                    if ($value->getSize() > $maxFileSize * 1024) {
                        $fail("The {$attribute} must not be greater than {$maxFileSize} kilobytes.");
                    }

                    return;
                }

                $fail("The {$attribute} must be an image file or valid path.");
            },
        ];
    }
}
