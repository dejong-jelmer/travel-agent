<?php

namespace App\Models;

use App\Models\Traits\ManagesImages;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property Trip $trip
 */
class Itinerary extends Model
{
    use HasFactory,
        ManagesImages,
        SoftDeletes;

    protected $fillable = [
        'trip_id',
        'title',
        'day_from',
        'day_to',
        'description',
        'accommodation',
        'activities',
        'remark',
        'order',
    ];

    protected $casts = [
        'activities' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        static::deleting(fn ($itinerary) => $itinerary->image()->delete());
        static::deleted(fn ($itinerary) => $itinerary->reOrder());
        static::restoring(fn ($itinerary) => $itinerary->image()->withTrashed()->restore());
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

    /**
     * Get the itinerary activities
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, never>
     */
    protected function activities(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => ! is_null($value)
                ? json_encode(
                    collect(
                        ! is_array($value)
                            ? array_map('trim', explode(',', $value))
                            : $value,
                    )->filter(fn ($item) => ! is_null($item) && $item !== '')->values()->all()
                )
                : '[]'
        );
    }
}
