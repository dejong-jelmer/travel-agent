<?php

namespace App\Enums\Booking;

use App\Enums\Traits\HasTranslatableLabel;
use App\Enums\Traits\Selectable;

enum Status: string
{
    use HasTranslatableLabel,
        Selectable;

    private const LABEL_KEY = 'booking.status';

    case New = 'new';
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Canceled = 'canceled';
    case Completed = 'completed';
}
