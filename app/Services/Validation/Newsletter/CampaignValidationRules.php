<?php

namespace App\Services\Validation\Newsletter;

use App\Enums\Newsletter\CampaignStatus;
use App\Models\Trip;
use App\Services\Validation\ImageValidationRules;
use Illuminate\Validation\Rule;

class CampaignValidationRules
{
    public static function basic(array $additions = []): array
    {
        return [
            'subject' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'preview_text' => ['nullable', 'string', 'max:255'],
            'status' => [
                Rule::enum(CampaignStatus::class)->except([CampaignStatus::Queued]),
            ],
            'scheduled_at' => ['nullable', 'required_if:status,scheduled', 'date', 'after:today'],
        ];
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
