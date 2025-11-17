<?php

namespace App\Services\Validation;

use Closure;
use Illuminate\Http\UploadedFile;
use Validator;

/**
 * Shared validation rules for image uploads.
 */
final class ImageValidationRules
{
    /**
     * Base image validation rules for new uploads only.
     *
     * Validates file type, mime type, and size for UploadedFile instances.
     *
     * @return array<int, string> Array of validation rules
     */
    public static function baseImage(): array
    {
        $maxFileSize = config('images.max_size');
        $mimes = implode(',', config('images.allowed_mimes'));

        return ['image', "mimes:{$mimes}", "max:{$maxFileSize}"];
    }

    /**
     * Image validation rules that accept both existing paths and new uploads.
     *
     * Validates:
     * - String paths: Accepted as-is (assumed to be existing database values)
     * - UploadedFile: Checks file type, mime type, and size
     *
     * @return array<int, string|Closure> Array containing validation closure
     */
    public static function baseImageOrString(): array
    {
        $maxFileSize = config('images.max_size');
        $mimes = config('images.allowed_mimes');

        return [
            function (string $attribute, mixed $value, Closure $fail) use ($maxFileSize, $mimes) {
                // Accept string paths for existing images (no validation needed)
                if (is_string($value)) {
                    return;
                }

                // Accept UploadedFile for new uploads
                if ($value instanceof UploadedFile) {
                    $allowedMimes = implode(',', $mimes);
                    // Validate as image
                    $validator = Validator::make(
                        [$attribute => $value],
                        [$attribute => ['image', "mimes:{$allowedMimes}", "max:{$maxFileSize}"]]
                    );

                    if ($validator->fails()) {
                        $fail($validator->errors()->first($attribute));
                    }

                    return;
                }

                $fail("The {$attribute} must be an image file or valid path.");
            },
        ];
    }
}
