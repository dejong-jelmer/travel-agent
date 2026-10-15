<?php

namespace App\Travel\Models;

use App\Travel\Database\Factories\AccommodationFactory;
use App\Travel\Enums\AccommodationType;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

#[UseFactory(AccommodationFactory::class)]
class Accommodation extends Model
{
    /** @use HasFactory<AccommodationFactory> */
    use HasFactory;

    protected $table = 'travel_accommodations';

    protected $fillable = [
        'city_id',
        'name',
        'type',
        'address',
        'phone',
        'latitude',
        'longitude',
        'check_in',
        'check_out',
        'map_image',
        'walk_directions',
    ];

    protected $casts = [
        'type' => AccommodationType::class,
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    protected static function booted(): void
    {
        // Legs are polymorphic, so the database cannot cascade them. Their pivot rows do cascade.
        // Use `deleted`, not `deleting`: a stay restricts the delete, and the arrival route must survive that.
        static::deleted(fn (self $accommodation) => $accommodation->legs()->delete());
    }

    /**
     * @return BelongsTo<City, $this>
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * @return MorphMany<Leg, $this>
     */
    public function legs(): MorphMany
    {
        return $this->morphMany(Leg::class, 'legable')->orderBy('position');
    }
}
