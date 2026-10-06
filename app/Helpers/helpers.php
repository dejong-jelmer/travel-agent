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

if (! function_exists('dropBlankListItems')) {
    /**
     * Drop blank entries from a list field and reindex it
     * The dynamic input lists always submit an empty trailing row, which must not count towards `max` rules
     * An entry made of several fields is blank when all its fields are, ignoring the `$ignoredKeys` (e.g. a preselected icon)
     *
     * @param  array<int, string>  $ignoredKeys
     */
    function dropBlankListItems(FormRequest $formRequest, string $field, array $ignoredKeys = []): void
    {
        $values = $formRequest->input($field);

        if (! is_array($values)) {
            return;
        }

        $isBlank = fn ($value) => is_null($value) || (is_string($value) && trim($value) === '');

        $formRequest->merge([
            $field => array_values(array_filter(
                $values,
                fn ($item) => is_array($item)
                    ? ! collect($item)->except($ignoredKeys)->every($isBlank)
                    : ! $isBlank($item)
            )),
        ]);
    }
}

if (! function_exists('nullifyEmptyHtml')) {
    /**
     * Null out rich text values that hold no visible content
     * TipTap submits "<p></p>" for an empty editor
     * Works on a single rich text field as well as on an array of them
     */
    function nullifyEmptyHtml(FormRequest $formRequest, string $field): void
    {
        $values = $formRequest->input($field);

        $nullifyIfEmpty = fn ($value) => preg_replace('/\s+/u', '', html_entity_decode(strip_tags((string) $value))) === ''
            ? null
            : $value;

        if (is_array($values)) {
            $formRequest->merge([$field => array_map($nullifyIfEmpty, $values)]);
        } elseif (is_string($values)) {
            $formRequest->merge([$field => $nullifyIfEmpty($values)]);
        }
    }
}

if (! function_exists('availableLocales')) {
    function availableLocales(): array
    {
        return array_keys(config('app.locales', [])) ?: ['en'];
    }
}
