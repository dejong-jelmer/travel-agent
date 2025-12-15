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
            SubscriberStatus::Active => __('newsletter.subscriber.status.active'),
            SubscriberStatus::Pending => __('newsletter.subscriber.status.pending'),
            SubscriberStatus::Expired => __('newsletter.subscriber.status.expired'),
            SubscriberStatus::Unsubscribed => __('newsletter.subscriber.status.unsubscribed'),
        };
    }
}
