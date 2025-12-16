<?php

namespace App\Enums\Newsletter;

use App\Enums\Traits\HasTranslatableLabel;

enum SubscriberStatus: string
{
    use HasTranslatableLabel;

    case Active = 'active';
    case Pending = 'pending';
    case Expired = 'expired';
    case Unsubscribed = 'unsubscribed';

    protected function getLabelKey(): string
    {
        return 'newsletter.subscriber.status';
    }
}
