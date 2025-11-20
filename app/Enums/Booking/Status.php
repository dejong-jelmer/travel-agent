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
}
