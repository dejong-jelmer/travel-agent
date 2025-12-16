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

    public function disabledOption(): bool
    {
        return match ($this) {
            self::Queued => true,
            self::Sent => true,
            self::Failed => true,
            default => false,
        };
    }
}
