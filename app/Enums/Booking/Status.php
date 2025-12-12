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

    public function label()
    {
        return match ($this) {
            Status::New => __('booking.status.new'),
            Status::Pending => __('booking.status.pending'),
            Status::Confirmed => __('booking.status.confirmed'),
            Status::Canceled => __('booking.status.canceled'),
            Status::Completed => __('booking.status.completed'),
        };
    }
}
