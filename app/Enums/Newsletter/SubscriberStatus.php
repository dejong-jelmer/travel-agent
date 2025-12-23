<?php

namespace App\Enums\Newsletter;

use App\Enums\Traits\HasTranslatableLabel;
use App\Enums\Traits\Selectable;

enum SubscriberStatus: string
{
    use HasTranslatableLabel,
        Selectable;

    case Active = 'active';
    case Pending = 'pending';
    case Expired = 'expired';
    case Unsubscribed = 'unsubscribed';

    protected function getLabelKey(): string
    {
        return 'newsletter.subscriber.status';
    }
}
