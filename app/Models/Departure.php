<?php

namespace App\Models;

use App\Enums\Booking\Status as BookingStatus;
use App\Enums\Trip\DepartureStatus;
use App\Enums\TripRequest\Status as TripRequestStatus;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

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
     * @return HasMany<Booking, $this>
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * @return HasMany<TripRequest, $this>
     */
    public function tripRequests(): HasMany
    {
        return $this->hasMany(TripRequest::class);
    }

    /**
     * Scope a query to the departures that still matter day to day: not cancelled, and the travelers not back yet.
     */
    #[Scope]
    protected function upcoming(Builder $query): void
    {
        $query->whereNull('cancelled_at')->whereDate('returns_on', '>=', today());
    }

    /**
     * Scope a query to the departures the public site shows: not cancelled and still to leave.
     */
    #[Scope]
    protected function visibleToVisitors(Builder $query): void
    {
        $query->whereNull('cancelled_at')->whereDate('departs_on', '>', today());
    }

    /**
     * Scope a query to load what the open requests on the departures ask, as requested_seats (the travelers of the
     * requests for a place) and interested_requests (the requests waiting for a place that comes free). Requests
     * reserve nothing, so these say what may come, not what is taken.
     */
    #[Scope]
    protected function withRequestCounts(Builder $query): void
    {
        $query->withSum([
            'tripRequests as requested_seats' => fn (Builder $requests) => $requests->whereIn('status', TripRequestStatus::open())->where('interest_only', false),
        ], 'travelers_count')->withCount([
            'tripRequests as interested_requests' => fn (Builder $requests) => $requests->whereIn('status', TripRequestStatus::open())->where('interest_only', true),
        ]);
    }

    /**
     * Scope a query to load the participant counts with the departures, as occupied_seats and
     * confirmed_participants, in the same query. Use it wherever more than one departure is shown: without it
     * occupiedSeats() and confirmedParticipants() run a query per departure.
     */
    #[Scope]
    protected function withParticipantCounts(Builder $query): void
    {
        $participants = DB::raw('total_adults + total_children');

        $query->withSum([
            'bookings as occupied_seats' => fn (Builder $bookings) => $bookings->whereIn('status', BookingStatus::seatOccupying()),
            'bookings as confirmed_participants' => fn (Builder $bookings) => $bookings->whereIn('status', BookingStatus::minimumCounting()),
        ], $participants);
    }

    /**
     * The number of places taken: the travelers of every booking that is not canceled or deleted.
     */
    public function occupiedSeats(): int
    {
        return $this->participantCount('occupied_seats', BookingStatus::seatOccupying());
    }

    /**
     * The number of travelers that count towards the minimum: those of confirmed and completed bookings.
     */
    public function confirmedParticipants(): int
    {
        return $this->participantCount('confirmed_participants', BookingStatus::minimumCounting());
    }

    public function availableSeats(): int
    {
        return max(0, $this->max_participants - $this->occupiedSeats());
    }

    public function isFull(): bool
    {
        return $this->occupiedSeats() >= $this->max_participants;
    }

    public function hasReachedMinimum(): bool
    {
        return $this->confirmedParticipants() >= $this->min_participants;
    }

    public function isGuaranteed(): bool
    {
        return $this->guaranteed_at !== null;
    }

    public function isCancelled(): bool
    {
        return $this->cancelled_at !== null;
    }

    /**
     * Whether the travelers are back: the day of return has passed.
     */
    public function isCompleted(): bool
    {
        return ! $this->isCancelled() && $this->returns_on->lt(today());
    }

    /**
     * The state of the departure, the first that applies of: cancelled, completed, full, guaranteed, open.
     */
    public function status(): DepartureStatus
    {
        return match (true) {
            $this->isCancelled() => DepartureStatus::Cancelled,
            $this->isCompleted() => DepartureStatus::Completed,
            $this->isFull() => DepartureStatus::Full,
            $this->isGuaranteed() => DepartureStatus::Guaranteed,
            default => DepartureStatus::Open,
        };
    }

    /**
     * The last day a visitor can request a place: the trip's minimum number of days in advance before the day of
     * departure, and at the latest the day before it.
     */
    public function lastRequestDate(?int $advanceDays): Carbon
    {
        return $this->departs_on->copy()->subDays(max(1, (int) $advanceDays));
    }

    /**
     * Whether the public site still offers this departure, given the trip's minimum number of days in advance.
     */
    public function acceptsRequests(?int $advanceDays): bool
    {
        return ! $this->isCancelled() && today()->lte($this->lastRequestDate($advanceDays));
    }

    /**
     * Whether the admin still has to decide if the departure goes ahead.
     */
    public function awaitsDecision(): bool
    {
        return ! $this->isGuaranteed() && ! $this->isCancelled() && ! $this->isCompleted();
    }

    /**
     * Whether the minimum is reached while the admin has not guaranteed the departure yet.
     */
    public function awaitsGuarantee(): bool
    {
        return $this->awaitsDecision() && $this->hasReachedMinimum();
    }

    /**
     * Whether the decision date has passed without a decision.
     */
    public function isDecisionOverdue(): bool
    {
        return $this->awaitsDecision() && $this->decision_date->lt(today());
    }

    /**
     * Whether the decision is due within the warning period of config/departures.php while the minimum is not
     * reached: the departure may have to be cancelled, and that has to happen on time.
     */
    public function isDecisionDueSoon(): bool
    {
        return $this->awaitsDecision()
            && ! $this->hasReachedMinimum()
            && $this->decision_date->betweenIncluded(today(), today()->addDays(config('departures.decision_warning_days')));
    }

    /**
     * Whether the admin has something to do for this departure now.
     */
    public function needsAttention(): bool
    {
        return $this->awaitsGuarantee() || $this->isDecisionOverdue() || $this->isDecisionDueSoon();
    }

    /**
     * Whether the departure can still change: it is not cancelled and the travelers are not back yet.
     */
    public function canBeEdited(): bool
    {
        return ! $this->isCancelled() && ! $this->isCompleted();
    }

    /**
     * Whether the departure can still be cancelled: it is not cancelled yet and has not left.
     */
    public function canBeCancelled(): bool
    {
        return ! $this->isCancelled() && $this->departs_on->gt(today());
    }

    /**
     * Why a booking of this many travelers cannot get places on this departure, as a message for the admin, or null
     * when it can. The count is only safe to act on while the departure row is locked, see BookingService.
     */
    public function refusalToBook(int $travelers): ?string
    {
        return match (true) {
            $this->isCancelled() => __('departure.booking.cancelled'),
            $this->departs_on->lte(today()) => __('departure.booking.departed'),
            $this->availableSeats() === 0 => __('departure.booking.full'),
            $this->availableSeats() < $travelers => __('departure.booking.not_enough_seats', [
                'available' => $this->availableSeats(),
                'requested' => $travelers,
            ]),
            default => null,
        };
    }

    /**
     * Promise the travelers that this departure goes ahead. The promise stands, also when bookings are canceled
     * afterwards: nothing in the app takes guaranteed_at away again.
     *
     * @throws \DomainException when the departure is cancelled
     */
    public function guarantee(): void
    {
        if ($this->isCancelled()) {
            throw new \DomainException('A cancelled departure cannot be guaranteed.');
        }

        if (! $this->isGuaranteed()) {
            $this->update(['guaranteed_at' => now()]);
        }
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

    /**
     * A participant count, from the withParticipantCounts() scope when it was applied and from a query otherwise.
     *
     * @param  list<BookingStatus>  $statuses
     */
    private function participantCount(string $attribute, array $statuses): int
    {
        if (array_key_exists($attribute, $this->attributes)) {
            return (int) $this->attributes[$attribute];
        }

        return (int) $this->bookings()
            ->whereIn('status', $statuses)
            ->sum(DB::raw('total_adults + total_children'));
    }

    private static function durationInDaysBetween(CarbonInterface $departsOn, CarbonInterface $returnsOn): int
    {
        return (int) $departsOn->copy()->startOfDay()->diffInDays($returnsOn->copy()->startOfDay()) + 1;
    }
}
