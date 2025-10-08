<?php

namespace App\Models;

use App\Enums\TravelerType;
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

class Booking extends Model
{
    use HasFactory,
        SoftDeletes;

    protected $fillable = [
        'product_id',
        'main_booker_id',
        'departure_date',
        'is_confirmed',
    ];

    protected $casts = [
        'departure_date' => 'datetime',
        'created_at' => 'datetime',
    ];

    protected $appends = [
        'departure_date_formatted',
        'created_at_formatted',
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
        return Attribute::get(
            fn () => $this->departure_date
                ? ucfirst($this->departure_date->locale('nl')->isoFormat('dddd D MMMM YYYY'))
                : null
        );
    }

    protected function createdAtFormatted(): Attribute
    {
        return Attribute::get(
            fn () => $this->created_at
                ? $this->created_at->isoFormat('DD-MM-YYYY')
                : null
        );
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Create a UUID
            $model->uuid = (string) Str::uuid();
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
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

    /**
     * Scope a query to only include new bookings.
     */
    #[Scope]
    protected function new(Builder $query): void
    {
        $query->where('new', 1);
    }

    /**
     * Scope a query to only include new bookings.
     */
    #[Scope]
    protected function future(Builder $query): void
    {
        $query->whereDate('departure_date', '>', now());
    }

    /**
     * Scope a query to only include new bookings.
     */
    #[Scope]
    protected function departDueNextMonth(Builder $query): void
    {
        $query->whereDate('departure_date', '>', now())->whereDate('departure_date', '<', now()->addMonth());
    }
}
