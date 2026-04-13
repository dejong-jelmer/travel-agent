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
        'name',
        'email',
        'phone',
        'preferred_month',
        'preferred_period_note',
        'travelers_count',
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
    ];

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
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
