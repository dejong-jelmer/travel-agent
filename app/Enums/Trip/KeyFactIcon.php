<?php

namespace App\Enums\Trip;

use App\Enums\Traits\HasTranslatableLabel;
use App\Enums\Traits\Selectable;

/**
 * Keep in sync with the icon map in resources/js/Components/Atoms/KeyFactIcon.vue: a case that is missing there
 * silently renders the default icon.
 */
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
