<?php

namespace App\Enums\Newsletter;

enum SubscriberStatus: string
{
    case Active = 'active';
    case Pending = 'pending';
    case Expired = 'expired';
    case Unsubscribed = 'unsubscribed';
}
