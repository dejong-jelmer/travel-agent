<?php

namespace App\Services\Validation\Newsletter;

use App\Enums\Newsletter\CampaignStatus;
use App\Models\Trip;
use App\Services\Traits\MergesRules;
use App\Services\Validation\ImageValidationRules;
use Illuminate\Validation\Rule;

class CampaignValidationRules
{
    use MergesRules;

    public static function basic(array $additions = []): array
    {
        return self::mergeRules([
            'subject' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'preview_text' => ['nullable', 'string', 'max:255'],
            'status' => [
                Rule::enum(CampaignStatus::class)->except([CampaignStatus::Sending]),
            ],
            'scheduled_at' => ['nullable', 'date', 'after:today'],
        ], $additions);
    }

    public static function heroImageStore(): array
    {

        return [
            'hero_image' => ['nullable', ...ImageValidationRules::baseImage()],
        ];
    }

    public static function heroImageUpdate(): array
    {
        return [
            'hero_image' => ['nullable', ...ImageValidationRules::baseImageOrString()],
        ];
    }

    public static function trips(): array
    {
        return [
            'trips' => ['nullable', 'array'],
            'trips.*' => ['integer', Rule::exists(Trip::class, 'id')],
        ];
    }
}
