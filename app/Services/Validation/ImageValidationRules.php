<?php

namespace App\Services\Validation;

use Closure;
use Illuminate\Support\Facades\Storage;

/**
 * Shared validation rules for image uploads.
 */
class ImageValidationRules
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
        $maxFileSize = config('app-settings.maxFileSize');
        $mimes = config('app-settings.mimes');

        return ['image', "mimes:{$mimes}", "max:{$maxFileSize}"];
    }

    /**
     * Image validation rules that accept both existing paths and new uploads.
     *
     * Validates:
     * - String paths: Checks file existence and extension
     * - UploadedFile: Checks file type, mime type, and size
     *
     * @return array<int, \Closure> Array containing validation closure
     */
    public static function baseImageOrString(): array
    {
        $maxFileSize = config('app-settings.maxFileSize');
        $mimes = config('app-settings.mimes');

        return [
            function (string $attribute, mixed $value, Closure $fail) use ($maxFileSize, $mimes) {
                // Accept string paths for existing images
                if (is_string($value)) {
                    // Verify file exists in storage
                    if (! Storage::disk('public')->exists('images/'.basename($value))) {
                        $fail("The {$attribute} references a non-existent image.");

                        return;
                    }
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
