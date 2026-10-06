<?php

namespace App\Enums\Trip;

use App\Enums\Traits\HasTranslatableLabel;
use App\Enums\Traits\Selectable;

enum KeyFactIcon: string
{
    use HasTranslatableLabel,
        Selectable;

    case Train = 'train';
    case Night = 'night';
    case Clock = 'clock';
    case Transfer = 'transfer';
    case Location = 'location';
    case Bed = 'bed';
    case Breakfast = 'breakfast';
    case Mountain = 'mountain';
    case Calendar = 'calendar';
    case Sun = 'sun';
    case Info = 'info';

    /**
     * The neutral icon, used for migrated key facts and whenever no icon is chosen.
     */
    public static function default(): self
    {
        return self::Info;
    }

    protected function getLabelKey(): string
    {
        return 'trip.key_fact_icon';
    }
}
