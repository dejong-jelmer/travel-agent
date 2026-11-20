<?php

namespace App\Enums\Booking;

use App\Enums\Traits\HasTranslatableLabel;
use App\Enums\Traits\Selectable;

enum Status: string
{
    use HasTranslatableLabel,
        Selectable;

    case New = 'new';
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Canceled = 'canceled';
    case Completed = 'completed';

    /**
     * Whether a booking in this status holds its places on a departure. Everything that is not canceled does, so a
     * booking that is still new or pending cannot lose its places to a later one.
     */
    public function occupiesSeat(): bool
    {
        return $this !== self::Canceled;
    }

    /**
     * Whether a booking in this status counts towards the minimum number of participants of a departure. Only
     * bookings that are certain do.
     */
    public function countsTowardsMinimum(): bool
    {
        return in_array($this, [self::Confirmed, self::Completed], true);
    }

    /**
     * @return list<self>
     */
    public static function seatOccupying(): array
    {
        return array_values(array_filter(self::cases(), fn (self $status) => $status->occupiesSeat()));
    }

    /**
     * @return list<self>
     */
    public static function minimumCounting(): array
    {
        return array_values(array_filter(self::cases(), fn (self $status) => $status->countsTowardsMinimum()));
    }

    protected function getLabelKey(): string
    {
        return 'booking.status';
    }
}
