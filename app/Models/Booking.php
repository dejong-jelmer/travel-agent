<?php

namespace App\Models;

use App\Enums\TravelerType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\Scope;

class Booking extends Model
{
    use HasFactory,
        SoftDeletes;

    protected $fillable = [
        'product_id',
        'main_booker_id',
        'departure_date',
        'confirmed',
    ];

    protected $casts = [
        'departure_date' => 'datetime',
        'created_at' => 'datetime',
    ];

    protected $appends = [
        'departure_date_formatted',
        'created_at_formatted',
    ];

    protected function departureDateFormatted(): Attribute
    {
        return Attribute::get(
            fn() =>
            $this->departure_date
                ? ucfirst($this->departure_date->locale('nl')->isoFormat('dddd D MMMM YYYY'))
                : null
        );
    }

    protected function createdAtFormatted(): Attribute
    {
        return Attribute::get(
            fn() =>
            $this->created_at
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
