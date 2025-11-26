<?php

namespace App\Models;

use App\Casts\MealCast;
use App\Casts\TransportCast;
use App\Models\Traits\ManagesImages;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Itinerary extends Model
{
    use HasFactory,
        ManagesImages,
        SoftDeletes;

    protected $fillable = [
        'trip_id',
        'title',
        'location',
        'description',
        'accommodation',
        'activities',
        'meals',
        'transport',
        'highlights',
        'remark',
        'order',
    ];

    protected $casts = [
        'activities' => 'array',
        'meals' => MealCast::class,
        'transport' => TransportCast::class,
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

    protected function activities(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => ! is_null($value)
                ? json_encode(
                    ! is_array($value)
                        ? array_map('trim', explode(',', $value))
                        : $value
                )
                : null
        );
    }
}
