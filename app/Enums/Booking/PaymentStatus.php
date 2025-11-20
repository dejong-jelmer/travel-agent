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
}
