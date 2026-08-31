<?php

namespace App\Models;

use App\Enums\Booking\PaymentStatus;
use App\Enums\Booking\Status;
use App\Enums\SettingKey;
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
use Illuminate\Support\Facades\DB;
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

    public const DEFAULT_MARGIN_BASIS_POINTS = 3500;

    protected array $formattedDates = [
        'departure_date' => ['format' => 'dddd LL'],
        'return_date' => ['format' => 'dddd LL'],
        'created_at' => ['format' => 'dddd LL - HH:mm'],
    ];

    protected $perPage = 15;

    protected $fillable = [
        'trip_id',
        'main_booker_id',
        'departure_date',
        'return_date',
        'has_accepted_conditions',
        'conditions_accepted_at',
        'has_confirmed',
        'confirmed_at',
        'status',
        'payment_status',
        'total_adults',
        'total_children',
        'trip_price_id',
        'price_per_person',
        'single_supplement',
        'base_total_price',
        'grand_total_price',
        'fees_and_funds',
        'margin_basis_points',
        'margin_in_percentage',
        'margin_amount',
        'fee_per_person',
        'calculated_price',
        'final_price',
        'internal_notes',
        'anonymized_at',
    ];

    protected $casts = [
        'has_accepted_conditions' => 'boolean',
        'has_confirmed' => 'boolean',
        'departure_date' => 'date',
        'return_date' => 'date',
        'status' => Status::class,
        'payment_status' => PaymentStatus::class,
        'fees_and_funds' => 'array',
        'margin_basis_points' => 'integer',
        'margin_in_percentage' => 'boolean',
        'margin_amount' => 'integer',
        'fee_per_person' => 'integer',
        'calculated_price' => 'integer',
        'final_price' => 'integer',
        'anonymized_at' => 'datetime',
        'conditions_accepted_at' => 'datetime',
        'confirmed_at' => 'datetime',
    ];

    protected $appends = [
        'departure_date_formatted',
        'return_date_formatted',
        'created_at_formatted',
        'status_label',
        'payment_status_label',
        'total_travelers',
        'display_price',
    ];

    protected $attributes = [
        'status' => Status::New->value,
        'payment_status' => PaymentStatus::Pending->value,
        'margin_in_percentage' => true,
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
        static::creating(function (self $booking) {
            if ($booking->margin_basis_points === null) {
                $booking->margin_basis_points = (int) Setting::get(
                    SettingKey::DefaultBookingMarginBasisPoints,
                    self::DEFAULT_MARGIN_BASIS_POINTS
                );
            }
        });

        static::saving(function (self $booking) {
            if ($booking->exists && $booking->isDirty([
                'margin_basis_points',
                'margin_in_percentage',
                'margin_amount',
                'fee_per_person',
                'fees_and_funds',
            ])) {
                $booking->calculated_price = $booking->computeCalculatedPrice();
            }
        });

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

    protected function returnDateFormatted(): Attribute
    {
        return Attribute::get(fn () => $this->getFormattedDate('return_date'));
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

    public function tripRequest(): HasOne
    {
        return $this->hasOne(TripRequest::class);
    }

    public function tripPrice(): BelongsTo
    {
        return $this->belongsTo(TripPrice::class);
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

    /**
     * Child travelers for this booking.
     *
     * @return HasMany<BookingTraveler, Booking>
     */
    public function children(): HasMany
    {
        return $this->travelers()->where('type', TravelerType::Child->value);
    }

    /**
     * Returns the number of adult travelers.
     * After anonymization: from the saved column.
     * Before anonymization: live count from the travelers relation.
     */
    public function getAdultsCount(): int
    {
        if ($this->isAnonymized()) {
            return $this->total_adults ?? 0;
        }

        return $this->adults()->count();
    }

    /**
     * Returns the number of child travelers.
     * After anonymization: from the saved column.
     * Before anonymization: live count from the travelers relation.
     */
    public function getChildrenCount(): int
    {
        if ($this->isAnonymized()) {
            return $this->total_children ?? 0;
        }

        return $this->children()->count();
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

    public function costItems(): HasMany
    {
        return $this->hasMany(BookingCostItem::class)->orderBy('sort_order');
    }

    /**
     * The destinations reachable via this booking's trip.
     */
    public function destinations(): HasManyDeep
    {
        return $this->hasManyDeepFromRelations(
            $this->trip(),
            (new Trip)->destinations()
        );
    }

    /**
     * Whether this booking's personal data has been anonymized.
     */
    public function isAnonymized(): bool
    {
        return $this->anonymized_at !== null;
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

    /**
     * Get the total traverellers for this booking .
     *
     * @return Attribute<int, never>
     */
    protected function totalTravelers(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->isAnonymized()
                ? ($this->total_adults ?? 0) + ($this->total_children ?? 0)
                : $this->travelers->count(),
        );
    }

    /**
     * Sum of all cost items in cents.
     *
     * @return Attribute<int, never>
     */
    protected function totalCost(): Attribute
    {
        return Attribute::get(
            fn () => (int) $this->costItems->sum('subtotal'),
        );
    }

    /**
     * Sum of the fees and funds snapshot in cents.
     *
     * @return Attribute<int, never>
     */
    protected function feesAndFundsTotal(): Attribute
    {
        return Attribute::get(
            fn () => (int) array_sum($this->fees_and_funds ?? []),
        );
    }

    /**
     * Visible sales price in cents: final_price override, else stored calculated_price.
     */
    protected function displayPrice(): Attribute
    {
        return Attribute::get(fn () => $this->final_price ?? $this->calculated_price);
    }

    /**
     * Recompute the sales price from cost items and the margin.
     *
     * Percentage margin works on the sales side: 35% margin means cost = 65% of sales.
     * calculated_price = total_cost / (1 - margin / 100).
     *
     * Fixed margin is added on top of the cost instead:
     * calculated_price = total_cost + margin_amount + fee_per_person * total_adults.
     *
     * The fees and funds snapshot is charged once per booking, on top of either
     * margin, so no margin is made on it.
     */
    public function computeCalculatedPrice(): int
    {
        return $this->computeMarginedPrice() + $this->feesAndFundsTotal;
    }

    /**
     * The sales price of the trip itself, before fees and funds.
     */
    private function computeMarginedPrice(): int
    {
        if (! $this->margin_in_percentage) {
            return $this->totalCost
                + ($this->margin_amount ?? 0)
                + ($this->fee_per_person ?? 0) * ($this->total_adults ?? 0);
        }

        $margin = ($this->margin_basis_points ?? 0) / 10000;

        if ($margin < 0 || $margin >= 1.0) {
            throw new \DomainException(
                "Invalid margin_basis_points ({$this->margin_basis_points}): margin must be between 0% and 100%."
            );
        }

        return (int) round($this->totalCost / (1 - $margin));
    }

    /**
     * Persist a freshly computed calculated_price.
     */
    public function recalculatePrice(): void
    {
        $this->load('costItems');
        $this->calculated_price = $this->computeCalculatedPrice();
        $this->saveQuietly();
    }

    /**
     * Replace this booking's cost items inside a transaction.
     *
     * @param  array<int, array{category: mixed, label: string, amount_per_person: int, quantity: int, sort_order?: int}>  $items
     */
    public function syncCostItems(array $items): void
    {
        DB::transaction(function () use ($items) {
            $this->costItems()->delete();

            foreach (array_values($items) as $index => $item) {
                $this->costItems()->make([
                    'category' => $item['category'],
                    'label' => $item['label'],
                    'amount_per_person' => $item['amount_per_person'],
                    'quantity' => $item['quantity'],
                    'sort_order' => $item['sort_order'] ?? $index,
                ])->saveQuietly();
            }

            $this->recalculatePrice();
        });
    }
}
