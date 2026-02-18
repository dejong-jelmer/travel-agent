<?php

namespace App\Models;

use App\Enums\Booking\PaymentStatus;
use App\Enums\Booking\Status;
use App\Enums\TravelerType;
use App\Models\Traits\HasFormattedDates;
use App\Models\Traits\Sortable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

/**
 * @property Trip $trip
 * @property BookingContact $contact
 * @property BookingTraveler $mainBooker
 */
class Booking extends Model
{
    use HasFactory,
        HasFormattedDates,
        HasRelationships,
        SoftDeletes,
        Sortable;

    protected array $formattedDates = [
        'departure_date' => ['format' => 'dddd LL'],
        'created_at' => ['format' => 'dddd LL - HH:mm'],
    ];

    protected $perPage = 15;

    protected $fillable = [
        'trip_id',
        'main_booker_id',
        'departure_date',
        'has_accepted_conditions',
        'has_confirmed',
        'status',
        'payment_status',
    ];

    protected $casts = [
        'has_accepted_conditions' => 'boolean',
        'has_confirmed' => 'boolean',
        'departure_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'status' => Status::class,
        'payment_status' => PaymentStatus::class,
    ];

    protected $appends = [
        'departure_date_formatted',
        'created_at_formatted',
        'status_label',
        'payment_status_label',
    ];

    protected $attributes = [
        'status' => Status::New->value,
        'payment_status' => PaymentStatus::Pending->value,
    ];

    // Sortable properties
    protected $searchable = ['reference'];

    protected $searchableRelations = ['trip.name', 'destinations.destinations.name'];

    protected $filterable = ['status', 'payment_status'];

    protected $sortable = ['id', 'reference', 'status', 'payment_status', 'departure_date', 'trip', 'destinations'];

    protected $sortableBelongsTo = [
        'trip' => [
            'table' => 'trips',
            'foreign_key' => 'trip_id',
            'column' => 'name',
        ],
    ];

    protected $sortableBelongsToMany = [
        'destinations' => [
            'relation' => 'destinations',
            'column' => 'name',
            'pivot_table' => 'destination_trip',
            'pivot_foreign_key' => 'trip_id',
            'pivot_related_key' => 'destination_id',
            'join_key' => 'trip_id',
        ],
    ];

    protected $defaultSort = [
        'column' => 'id',
        'direction' => 'desc',
    ];

    protected static function booted()
    {
        static::created(function ($booking) {
            $year = now()->format('Y');
            $booking->reference = "{$year}-".str_pad($booking->id, 6, '0', STR_PAD_LEFT);
            $booking->saveQuietly();
        });

        static::updated(function ($booking) {
            $changes = $booking->getChanges();
            $original = $booking->getOriginal();
            unset($changes['updated_at'], $changes['created_at']);
            foreach ($changes as $field => $newValue) {
                BookingChange::create([
                    'booking_id' => $booking->id,
                    'user_id' => Auth::user()->id ?? null,
                    'model_type' => self::class,
                    'model_id' => $booking->id,
                    'field' => $field,
                    'old_value' => $original[$field] ?? null,
                    'new_value' => $newValue,
                ]);
            }
        });

        static::deleted(function ($booking) {
            BookingChange::create([
                'booking_id' => $booking->id,
                'admin_id' => Auth::user()->id ?? null,
                'model_type' => self::class,
                'model_id' => $booking->id,
                'field' => 'deleted',
                'old_value' => json_encode($booking->getOriginal()),
                'new_value' => null,
            ]);
        });
    }

    protected function departureDateFormatted(): Attribute
    {
        return Attribute::get(fn () => $this->getFormattedDate('departure_date'));
    }

    protected function createdAtFormatted(): Attribute
    {
        return Attribute::get(fn () => $this->getFormattedDate('created_at'));
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Create a UUID
            $model->uuid = (string) Str::uuid();
        });
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function travelers(): HasMany
    {
        return $this->hasMany(BookingTraveler::class)
            ->orderByRaw('CASE WHEN id = ? THEN 0 ELSE 1 END', [$this->main_booker_id]);
    }

    public function adults(): HasMany
    {
        return $this->travelers()->where('type', TravelerType::Adult->value);
    }

    public function children(): HasMany
    {
        return $this->travelers()->where('type', TravelerType::Child->value);
    }

    public function mainBooker(): BelongsTo
    {
        return $this->belongsTo(BookingTraveler::class, 'main_booker_id');
    }

    public function contact(): HasOne
    {
        return $this->hasOne(BookingContact::class);
    }

    public function changes(): HasMany
    {
        return $this->hasMany(BookingChange::class);
    }

    public function destinations(): HasManyDeep
    {
        return $this->hasManyDeepFromRelations(
            $this->trip(), (new Trip)->destinations()
        );
    }

    /**
     * Scope a query to only include new bookings.
     */
    #[Scope]
    protected function new(Builder $query): void
    {
        $query->where('status', Status::New);
    }

    /**
     * Scope a query to only include bookings with a departure date in the future.
     */
    #[Scope]
    protected function upcoming(Builder $query): void
    {
        $query->whereDate('departure_date', '>', now());
    }

    /**
     * Scope a query to only include bookings with a departure date in the upcoming month.
     */
    #[Scope]
    protected function upcomingMonth(Builder $query): void
    {
        $query->whereBetween('departure_date', [
            now()->startOfDay(),
            now()->addMonth()->endOfDay(),
        ]);
    }

    /**
     * Get the status label.
     *
     * @return Attribute<string, never>
     */
    protected function statusLabel(): Attribute
    {
        return Attribute::get(fn () => $this->status->label());
    }

    /**
     * Get the payment_status label.
     *
     * @return Attribute<string, never>
     */
    protected function paymentStatusLabel(): Attribute
    {
        return Attribute::get(fn () => $this->payment_status->label());
    }
}
