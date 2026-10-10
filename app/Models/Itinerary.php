<?php

namespace App\Models;

use App\Enums\Trip\ItineraryType;
use App\Models\Traits\ManagesImages;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property Trip $trip
 * @property Image|null $image
 */
class Itinerary extends Model
{
    use HasFactory,
        ManagesImages,
        SoftDeletes;

    protected $fillable = [
        'trip_id',
        'type',
        'title',
        'day_from',
        'day_to',
        'description',
        'accommodation',
        'remark',
        'order',
    ];

    protected $casts = [
        'type' => ItineraryType::class,
    ];

    protected static function boot()
    {
        parent::boot();
        static::deleting(fn ($itinerary) => $itinerary->image()->delete());
        static::deleted(fn ($itinerary) => $itinerary->reOrder());
        static::restoring(fn ($itinerary) => $itinerary->image()->withTrashed()->restore());

        static::saved(fn ($itinerary) => $itinerary->trip->recalculateDuration());
        static::deleted(fn ($itinerary) => $itinerary->trip->recalculateDuration());
        static::restored(fn ($itinerary) => $itinerary->trip->recalculateDuration());
    }

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function reOrder(): void
    {
        $order = 1;
        $itineraries = static::where('trip_id', $this->trip_id)
            ->where('id', '!=', $this->id)
            ->orderBy('order')
            ->get();

        foreach ($itineraries as $itinerary) {
            $itinerary->order = $order++;
            $itinerary->save();
        }
    }
}
