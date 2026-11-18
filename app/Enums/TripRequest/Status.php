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

    /**
     * The statuses of a request the admin still has to handle.
     *
     * @return list<self>
     */
    public static function open(): array
    {
        return [self::New, self::Contacted];
    }

    protected function getLabelKey(): string
    {
        return 'enum.trip_requests';
    }
}
