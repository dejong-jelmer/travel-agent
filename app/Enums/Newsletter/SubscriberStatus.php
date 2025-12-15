<?php

namespace App\Enums\Newsletter;

enum SubscriberStatus: string
{
    case Active = 'active';
    case Pending = 'pending';
    case Expired = 'expired';
    case Unsubscribed = 'unsubscribed';

    public function label(): string
    {
        return match ($this) {
            self::Active => __('newsletter.subscriber.status.active'),
            self::Pending => __('newsletter.subscriber.status.pending'),
            self::Expired => __('newsletter.subscriber.status.expired'),
            self::Unsubscribed => __('newsletter.subscriber.status.unsubscribed'),
        };
    }
}
