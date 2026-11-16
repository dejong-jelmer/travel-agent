<?php

namespace App\Models;

use App\Enums\TripRequest\Status;
use App\Models\Traits\HasFormattedDates;
use App\Models\Traits\Sortable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property Trip $trip
 * @property Booking|null $booking
 * @property Departure|null $departure
 */
class TripRequest extends Model
{
    use HasFactory, HasFormattedDates, Sortable;

    protected $perPage = 15;

    protected array $formattedDates = [
        'created_at' => ['format' => 'DD-MM-YYYY HH:mm'],
    ];

    protected $appends = [
        'status_label',
        'created_at_formatted',
    ];

    protected array $searchable = ['name', 'email', 'phone'];

    protected array $searchableRelations = ['trip.name'];

    protected array $filterable = ['status'];

    protected array $sortable = [
        'id',
        'name',
        'email',
        'status',
        'created_at',
        'trip',
    ];

    protected array $sortableBelongsTo = [
        'trip' => [
            'table' => 'trips',
            'foreign_key' => 'trip_id',
            'column' => 'name',
        ],
    ];

    protected array $defaultSort = [
        'column' => 'created_at',
        'direction' => 'desc',
    ];

    protected $fillable = [
        'trip_id',
        'departure_id',
        'name',
        'email',
        'phone',
        'preferred_month',
        'preferred_period_note',
        'travelers_count',
        'single_room',
        'interest_only',
        'departure_station',
        'notes',
        'status',
        'booking_id',
        'consent_privacy',
        'consent_privacy_at',
    ];

    protected $casts = [
        'status' => Status::class,
        'consent_privacy' => 'boolean',
        'consent_privacy_at' => 'datetime',
        'travelers_count' => 'integer',
        'single_room' => 'boolean',
        'interest_only' => 'boolean',
    ];

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * The departure a request for a group trip is made on. Null for an individual trip.
     *
     * @return BelongsTo<Departure, $this>
     */
    public function departure(): BelongsTo
    {
        return $this->belongsTo(Departure::class);
    }

    /**
     * The number of travelers as the mails and the admin show it. The form offers "more than 6" as 7; requests made
     * before that offered "more than 8" as 9.
     */
    public function travelersLabel(): string
    {
        return match ($this->travelers_count) {
            null => __('trip_request.mail.notification_not_specified'),
            7 => __('trip_request.mail.notification_travelers_more_than_6'),
            9 => __('trip_request.mail.notification_travelers_more_than_8'),
            default => (string) $this->travelers_count,
        };
    }

    /**
     * What the visitor asked for, as label and value rows for the mails: the departure, travelers and single room of
     * a group trip, or the preferred period, travelers and station of an individual trip.
     *
     * @return list<array{label: string, value: string}>
     */
    public function summaryRows(): array
    {
        $notSpecified = __('trip_request.mail.notification_not_specified');
        $row = fn (string $key, string $value) => ['label' => __("trip_request.mail.notification_label_{$key}"), 'value' => $value];

        if ($this->departure) {
            return array_values(array_filter([
                $row('departure', $this->departure->departs_on->format('d-m-Y').' – '.$this->departure->returns_on->format('d-m-Y')),
                $this->interest_only ? $row('interest', __('trip_request.mail.interest_only')) : null,
                $row('travelers', $this->travelersLabel()),
                $row('single_room', __($this->single_room ? 'trip_request.mail.yes' : 'trip_request.mail.no')),
            ]));
        }

        return array_values(array_filter([
            $row('period', $this->preferred_month ?? $notSpecified),
            $this->preferred_period_note ? $row('period_note', $this->preferred_period_note) : null,
            $row('travelers', $this->travelersLabel()),
            $row('station', $this->departure_station ?? $notSpecified),
        ]));
    }

    protected function statusLabel(): Attribute
    {
        return Attribute::get(fn () => $this->status->label());
    }

    protected function createdAtFormatted(): Attribute
    {
        return Attribute::get(fn () => $this->getFormattedDate('created_at'));
    }

    #[Scope]
    protected function new(Builder $query): void
    {
        $query->where('status', Status::New);
    }
}
