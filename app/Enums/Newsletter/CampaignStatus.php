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
            CampaignStatus::Draft => __('newsletter.campaign.status.draft'),
            CampaignStatus::Scheduled => __('newsletter.campaign.status.scheduled'),
            CampaignStatus::Queued => __('newsletter.campaign.status.queued'),
            CampaignStatus::Sent => __('newsletter.campaign.status.sent'),
            CampaignStatus::Failed => __('newsletter.campaign.status.failed'),
        };
    }
}
