<?php

namespace App\Enums;

use App\Enums\Traits\Selectable;

enum Transport: string
{
    use Selectable;
    case Train = 'train';
    case Ferry = 'ferry';
    case Bus = 'bus';
    case Taxi = 'taxi';
    case Transfer = 'transfer';
    case Airplane = 'airplane';

    public function label(): string
    {
        return match ($this) {
            self::Train => __('itinerary.transport.train'),
            self::Ferry => __('itinerary.transport.ferry'),
            self::Bus => __('itinerary.transport.bus'),
            self::Taxi => __('itinerary.transport.taxi'),
            self::Transfer => __('itinerary.transport.transfer'),
            self::Airplane => __('itinerary.transport.airplane'),
        };
    }
}
