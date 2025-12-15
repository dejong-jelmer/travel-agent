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

    public function label(): string
    {
        return match ($this) {
            self::Draft => __('newsletter.campaign.status.draft'),
            self::Scheduled => __('newsletter.campaign.status.scheduled'),
            self::Queued => __('newsletter.campaign.status.queued'),
            self::Sent => __('newsletter.campaign.status.sent'),
            self::Failed => __('newsletter.campaign.status.failed'),
        };
    }
}
