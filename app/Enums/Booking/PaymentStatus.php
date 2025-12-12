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

    public function label()
    {
        return match ($this) {
            PaymentStatus::Pending => __('booking.payment_status.pending'),
            PaymentStatus::PartiallyPaid => __('booking.payment_status.partial_paid'),
            PaymentStatus::Paid => __('booking.payment_status.paid'),
            PaymentStatus::Refunded => __('booking.payment_status.refunded'),
            PaymentStatus::PartiallyRefunded => __('booking.payment_status.partially_refunded'),
            PaymentStatus::Failed => __('booking.payment_status.failed'),
        };
    }
}
