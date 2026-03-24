<?php

namespace App\Services\Validation;

use App\Enums\BlogPost\Status;
use App\Services\Traits\MergesRules;
use Illuminate\Validation\Rule;

class BlogPostValidationRules
{
    use MergesRules;

    public static function basic(array $additions = []): array
    {
        return self::mergeRules([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'featured_image' => array_merge(['nullable'], ImageValidationRules::baseImage()),
            'meta_title' => ['nullable', 'string', 'max:60'],
            'meta_description' => ['nullable', 'string', 'max:160'],
            'status' => ['required', Rule::enum(Status::class)],
        ], $additions);
    }

    public static function basicUpdate(array $additions = []): array
    {
        return self::mergeRules([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'featured_image' => array_merge(['nullable'], ImageValidationRules::baseImageOrString()),
            'meta_title' => ['nullable', 'string', 'max:60'],
            'meta_description' => ['nullable', 'string', 'max:160'],
            'status' => ['required', Rule::enum(Status::class)],
        ], $additions);
    }
}
