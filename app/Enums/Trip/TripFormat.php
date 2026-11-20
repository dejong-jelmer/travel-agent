<?php

namespace App\Enums\Trip;

use App\Enums\Traits\HasTranslatableLabel;
use App\Enums\Traits\Selectable;

/**
 * How a trip is sold: on a date the customer picks, or on fixed departures shared by a group.
 */
enum TripFormat: string
{
    use HasTranslatableLabel,
        Selectable;

    case Individual = 'individual';
    case Group = 'group';

    /**
     * One sentence for visitors on what this format means, shown on the trip page.
     */
    public function description(): string
    {
        return __("enum.trip_format_descriptions.{$this->value}");
    }

    /**
     * Whether the public pages point this format out. Individual is how most trips are sold, so only the exception,
     * a group trip, gets a label.
     */
    public function isShownToVisitors(): bool
    {
        return $this === self::Group;
    }

    /**
     * @return array{value: string, label: string, description: string}
     */
    public function toDisplay(): array
    {
        return [
            'value' => $this->value,
            'label' => $this->label(),
            'description' => $this->description(),
        ];
    }

    protected function getLabelKey(): string
    {
        return 'enum.trip_formats';
    }
}
