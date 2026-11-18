<?php

namespace App\Events;

use App\Models\Departure;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * A booking brought the confirmed participants of a departure to its minimum, while the departure is not
 * guaranteed yet. Guaranteeing stays a decision of the admin.
 */
class DepartureMinimumReached
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Departure $departure
    ) {}
}
