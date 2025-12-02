<?php

namespace App\Enums\Newsletter;

use App\Enums\Traits\Selectable;

enum CampaignStatus: string
{
    use Selectable;

    case Draft = 'draft';
    case Scheduled = 'scheduled';
    case Sending = 'sending';
    case Sent = 'sent';
}
