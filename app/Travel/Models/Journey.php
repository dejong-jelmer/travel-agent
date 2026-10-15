<?php

namespace App\Travel\Models;

use App\Models\Booking;
use App\Travel\Database\Factories\JourneyFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

#[UseFactory(JourneyFactory::class)]
class Journey extends Model
{
    /** @use HasFactory<JourneyFactory> */
    use HasFactory;

    protected $table = 'travel_journeys';

    protected $fillable = [
        'booking_id',
        'label',
        'date',
        'position',
    ];

    protected $casts = [
        'date' => 'date',
        'position' => 'integer',
    ];

    protected static function booted(): void
    {
        // Legs are polymorphic, so the database cannot cascade them. Their pivot rows do cascade.
        static::deleted(fn (self $journey) => $journey->legs()->delete());
    }

    /**
     * @return BelongsTo<Booking, $this>
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * @return MorphMany<Leg, $this>
     */
    public function legs(): MorphMany
    {
        return $this->morphMany(Leg::class, 'legable')->orderBy('position');
    }
}
