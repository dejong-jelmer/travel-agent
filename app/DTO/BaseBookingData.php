<?php

namespace App\DTO;

use App\DTO\Traits\ArrayableDTO;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;

abstract class BaseBookingData implements Arrayable
{
    use ArrayableDTO;

    public function __construct(
        public readonly ?Product $_trip,
        public readonly int $_main_booker,
        public readonly array $_adult,
        public readonly array $_child,
        public readonly BookingContactData $_contact,
        public readonly ?Carbon $_date = null,
        public readonly ?bool $_conditions = null,
        public readonly ?bool $_confirmed = null
    ) {}
}
