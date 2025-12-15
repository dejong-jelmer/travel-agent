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
            Transport::Train => __('itinerary.transport.train'),
            Transport::Ferry => __('itinerary.transport.ferry'),
            Transport::Bus => __('itinerary.transport.bus'),
            Transport::Taxi => __('itinerary.transport.taxi'),
            Transport::Transfer => __('itinerary.transport.transfer'),
            Transport::Airplane => __('itinerary.transport.airplane'),
        };
    }
}
