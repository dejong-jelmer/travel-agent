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
            'image' => ['nullable', ...self::baseImageOrString()],
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
