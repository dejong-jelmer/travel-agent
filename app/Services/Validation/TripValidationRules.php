<?php

namespace App\Services\Validation;

use App\Enums\Trip\ItemCategory;
use App\Enums\Trip\ItemType;
use App\Services\Traits\MergesRules;
use Illuminate\Validation\Rule;

class TripValidationRules
{
    use MergesRules;

    public static function basic(array $additions = []): array
    {
        return self::mergeRules([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255'],
            'highlights' => ['required', 'array'],
            'highlights.*' => [
                'nullable',
                'string',
                'max:255',
            ],
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

    public static function items(): array
    {
        return [
            'items' => ['nullable', 'array'],
            'items.*.type' => ['required', 'string', Rule::enum(ItemType::class)],
            'items.*.category' => ['required', 'string', Rule::enum(ItemCategory::class)],
            'items.*.item' => ['required', 'string', 'max:1'],
        ];
    }
}
