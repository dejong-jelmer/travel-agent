<?php

namespace App\Enums\Newsletter;

use App\Enums\Traits\HasTranslatableLabel;
use App\Enums\Traits\Selectable;

enum CampaignStatus: string
{
    use HasTranslatableLabel,
        Selectable;

    case Draft = 'draft';
    case Scheduled = 'scheduled';
    case Queued = 'queued';
    case Sent = 'sent';
    case Failed = 'failed';

    protected function getLabelKey(): string
    {
        return 'newsletter.campaign.status';
    }

    public function extraOptions(): array
    {
        return [
            'disabled' => $this->isDisabled(),
        ];
    }

    public function isDisabled(): bool
    {
        return match ($this) {
            self::Queued, self::Sent, self::Failed => true,
            default => false,
        };
    }
}
