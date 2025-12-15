<?php

namespace App\Enums\Booking;

use App\Enums\Traits\Selectable;

enum Status: string
{
    use Selectable;
    case New = 'new';
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Canceled = 'canceled';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::New => __('booking.status.new'),
            self::Pending => __('booking.status.pending'),
            self::Confirmed => __('booking.status.confirmed'),
            self::Canceled => __('booking.status.canceled'),
            self::Completed => __('booking.status.completed'),
        };
    }
}
