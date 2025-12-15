<?php

namespace App\Enums\Booking;

use App\Enums\Traits\Selectable;

enum PaymentStatus: string
{
    use Selectable;
    case Pending = 'pending';
    case PartiallyPaid = 'partial_paid';
    case Paid = 'paid';
    case Refunded = 'refunded';
    case PartiallyRefunded = 'partially_refunded';
    case Failed = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => __('booking.payment_status.pending'),
            self::PartiallyPaid => __('booking.payment_status.partial_paid'),
            self::Paid => __('booking.payment_status.paid'),
            self::Refunded => __('booking.payment_status.refunded'),
            self::PartiallyRefunded => __('booking.payment_status.partially_refunded'),
            self::Failed => __('booking.payment_status.failed'),
        };
    }
}
