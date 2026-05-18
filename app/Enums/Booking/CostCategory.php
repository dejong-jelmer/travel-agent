<?php

namespace App\Enums\Booking;

use App\Enums\Traits\HasTranslatableLabel;
use App\Enums\Traits\Selectable;

enum CostCategory: string
{
    use HasTranslatableLabel,
        Selectable;

    case Train = 'train';
    case Accommodation = 'accommodation';
    case Transfer = 'transfer';
    case Ticket = 'ticket';
    case Extra = 'extra';

    protected function getLabelKey(): string
    {
        return 'booking.cost_category';
    }
}
