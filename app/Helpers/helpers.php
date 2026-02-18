<?php

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

if (! function_exists('randomPrice')) {
    function randomPrice(int $min = 200, int $max = 5000): float
    {
        return (float) fake()->randomFloat(2, $min, $max);
    }
}

if (! function_exists('emptyFormRequestToArray')) {
    /**
     * Convert null form request fields to empty arrays
     * Useful for multi-select fields that may be absent from the request
     */
    function emptyFormRequestToArray(FormRequest $formRequest, string|array $fields): void
    {
        foreach (Arr::wrap($fields) as $field) {
            if ($formRequest->missing($field) || $formRequest->input($field) === null) {
                $formRequest->merge([$field => []]);
            }
        }
    }
}

if (! function_exists('availableLocales')) {
    function availableLocales(): array
    {
        return array_keys(config('app.locales')) ?: ['en'];
    }
}
