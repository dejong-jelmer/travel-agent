<?php

namespace App\Services\Validation;

use Closure;
use Illuminate\Support\Facades\Storage;

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
     * - String paths: Checks file existence and extension
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
                // Accept string paths for existing images
                if (is_string($value)) {
                    // Handle both hash filenames and full paths
                    $filename = basename($value);
                    $pregMimes = implode('|', $mimes);

                    if (! preg_match("/^[a-zA-Z0-9_\-]+\.({$pregMimes})$/i", $filename)) {
                        $fail("The {$attribute} contains invalid characters.");

                        return;
                    }

                    // Verify file exists in storage (check "images/{hash}" path)
                    if (! Storage::disk('public')->exists('images/'.$filename)) {
                        $fail("The {$attribute} references a non-existent image.");

                        return;
                    }

                    // Validate extension (works for both hash and original filenames)
                    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    if (! in_array($extension, $mimes)) {
                        $fail("The {$attribute} must be a valid image file.");
                    }

                    return;
                }

                // Accept UploadedFile for new uploads
                if ($value instanceof \Illuminate\Http\UploadedFile) {
                    $allowedMimes = implode(',', config('images.allowed_mimes'));
                    // Validate as image
                    $validator = \Validator::make(
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
