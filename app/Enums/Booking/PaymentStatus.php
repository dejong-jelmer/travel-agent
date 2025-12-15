<?php

namespace App\Enums\Booking;

use App\Enums\Traits\HasTranslatableLabel;
use App\Enums\Traits\Selectable;

enum PaymentStatus: string
{
    use Selectable,
        HasTranslatableLabel;

    private const LABEL_KEY = 'booking.payment_status';

    case Pending = 'pending';
    case PartiallyPaid = 'partial_paid';
    case Paid = 'paid';
    case Refunded = 'refunded';
    case PartiallyRefunded = 'partially_refunded';
    case Failed = 'failed';
}
