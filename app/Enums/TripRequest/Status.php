<?php

namespace App\Enums\TripRequest;

use App\Enums\Traits\HasTranslatableLabel;
use App\Enums\Traits\Selectable;

enum Status: string
{
    use HasTranslatableLabel,
        Selectable;

    case New = 'new';
    case Contacted = 'contacted';
    case Converted = 'converted';
    case Archived = 'archived';

    protected function getLabelKey(): string
    {
        return 'enum.trip_requests';
    }
}
