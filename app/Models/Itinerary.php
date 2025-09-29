<?php

namespace App\Models;

use App\Casts\MealsCast;
use App\Casts\TransportCast;
use App\Models\Traits\HasStoreableImages;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;


class Itinerary extends Model
{
    use HasFactory,
        SoftDeletes,
        HasStoreableImages;

    protected $fillable = [
        'product_id',
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
        'meals' => MealsCast::class,
        'transport' => TransportCast::class,
    ];

    protected static function boot()
    {
        parent::boot();
        static::deleting(fn($itinerary) => $itinerary->image()->delete());
        static::deleted(fn($itinerary) => $itinerary->reOrder());
        static::restoring(fn($itinerary) => $itinerary->image()->withTrashed()->restore());
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function reOrder(): void
    {
        $order = 1;
        $itineraries = static::where('product_id', $this->product_id)
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
            set: fn($value) => ! is_null($value)
                ? json_encode(
                    ! is_array($value)
                        ? array_map('trim', explode(',', $value))
                        : $value
                )
                : null
        );
    }
}
