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
            'scheduled_at' => [
                'nullable',
                'required_if:status,scheduled',
                'date',
                // Alleen een geplande campagne moet in de toekomst liggen; een reeds
                // verzonden campagne houdt zijn oude datum en mag gewoon bewerkt worden.
                Rule::when(
                    fn ($input) => $input->status === CampaignStatus::Scheduled->value,
                    ['after:today']
                ),
            ],
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
