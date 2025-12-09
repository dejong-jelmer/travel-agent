<?php

namespace App\Enums\Newsletter;

enum CampaignSubscriberStatus: string
{
    case Queued = 'queued';
    case Sent = 'sent';
    case Failed = 'failed';
}
