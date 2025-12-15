<?php

namespace App\Enums\Newsletter;

use App\Enums\Traits\HasTranslatableLabel;

enum SubscriberStatus: string
{
    use HasTranslatableLabel;

    private const LABEL_KEY = 'newsletter.subscriber.status';

    case Active = 'active';
    case Pending = 'pending';
    case Expired = 'expired';
    case Unsubscribed = 'unsubscribed';
}
