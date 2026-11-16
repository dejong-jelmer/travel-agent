<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * A fixed departure of a group trip, which travelers book a place on.
 *
 * @property Trip $trip
 * @property Carbon $departs_on
 * @property Carbon $returns_on
 * @property int $min_participants
 * @property int $max_participants
 * @property int|null $price_per_person
 * @property int|null $single_supplement
 * @property Carbon $decision_date
 * @property Carbon|null $guaranteed_at
 * @property Carbon|null $cancelled_at
 * @property string|null $cancellation_reason
 */
class Departure extends Model
{
    /** @use HasFactory<\Database\Factories\DepartureFactory> */
    use HasFactory;

    protected $fillable = [
        'trip_id',
        'departs_on',
        'returns_on',
        'min_participants',
        'max_participants',
        'price_per_person',
        'single_supplement',
        'decision_date',
        'guaranteed_at',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected $casts = [
        'departs_on' => 'date',
        'returns_on' => 'date',
        'min_participants' => 'integer',
        'max_participants' => 'integer',
        'price_per_person' => 'integer',
        'single_supplement' => 'integer',
        'decision_date' => 'date',
        'guaranteed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $departure) {
            // Without a date of its own, the decision is due on the last day the law allows
            $departure->decision_date ??= $departure->latestDecisionDate();
        });
    }

    /**
     * @return BelongsTo<Trip, $this>
     */
    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class)->withTrashed();
    }

    /**
     * The length of the trip in days, counting both the day of departure and the day of return.
     */
    public function durationInDays(): int
    {
        return self::durationInDaysBetween($this->departs_on, $this->returns_on);
    }

    /**
     * The last day on which the decision whether this departure goes ahead may be made.
     */
    public function latestDecisionDate(): Carbon
    {
        return self::latestDecisionDateFor($this->departs_on, $this->returns_on);
    }

    /**
     * The last day on which travelers may be told that a departure on these dates is canceled for lack of
     * participants, from the deadlines in config/departures.php.
     */
    public static function latestDecisionDateFor(CarbonInterface $departsOn, CarbonInterface $returnsOn): Carbon
    {
        $duration = self::durationInDaysBetween($departsOn, $returnsOn);

        foreach (config('departures.decision_deadlines') as $deadline) {
            if ($duration >= $deadline['min_duration_days']) {
                return Carbon::instance($departsOn)->startOfDay()->subDays($deadline['days_before_departure']);
            }
        }

        throw new \LogicException("No decision deadline is configured for a trip of {$duration} days.");
    }

    private static function durationInDaysBetween(CarbonInterface $departsOn, CarbonInterface $returnsOn): int
    {
        return (int) $departsOn->copy()->startOfDay()->diffInDays($returnsOn->copy()->startOfDay()) + 1;
    }
}
