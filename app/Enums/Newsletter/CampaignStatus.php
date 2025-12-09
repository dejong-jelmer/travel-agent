<?php

namespace App\Enums\Newsletter;

use App\Enums\Traits\Selectable;

enum CampaignStatus: string
{
    use Selectable;

    case Draft = 'draft';
    case Scheduled = 'scheduled';
    case Queued = 'queued';
    case Sent = 'sent';
    case Failed = 'failed';

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
