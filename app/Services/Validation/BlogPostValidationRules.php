<?php

namespace App\Services\Validation;

use App\Enums\BlogPost\Status;
use App\Models\Trip;
use Illuminate\Validation\Rule;

class BlogPostValidationRules
{
    public static function basic(array $additions = []): array
    {
        return array_merge([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'summary' => ['nullable', 'string', 'max:1000'],
            'featured_image' => array_merge(['nullable'], ImageValidationRules::baseImage()),
            ...ImageValidationRules::alt('featured_image_alt'),
            'meta_title' => ['nullable', 'string', 'max:60'],
            'meta_description' => ['nullable', 'string', 'max:160'],
            'status' => ['required', Rule::enum(Status::class)],
            'trips' => ['nullable', 'array'],
            'trips.*' => ['integer', 'distinct', Rule::exists(Trip::class, 'id')->withoutTrashed()],
        ], $additions);
    }
}
