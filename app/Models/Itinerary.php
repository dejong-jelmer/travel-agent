<?php

namespace App\Models;

use App\Traits\StoreableImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\Meals;
use App\Enums\Transport;
use Illuminate\Database\Eloquent\Casts\Attribute;


class Itinerary extends Model
{
    use HasFactory,
        SoftDeletes,
        StoreableImage;

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
        'meals' => 'array',
        'transport' => 'array',
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

    protected function meals(): Attribute
    {
        return Attribute::make(
            get: fn($value) => collect(json_decode($value ?? '', true))
                ->map(fn($meal) => Meals::from($meal))
                ->all(),
            set: fn($value) => json_encode(
                collect($value)
                    ->map(fn($value) => $value instanceof Meals ? $value->value : $value)
                    ->all()
            )
        );
    }

    protected function transport(): Attribute
    {
        return Attribute::make(
            get: fn($value) => collect(json_decode($value ?? '', true))
                ->map(fn($transport) => Transport::from($transport))
                ->all(),
            set: fn($value) => json_encode(
                collect($value)
                    ->map(fn($value) => $value instanceof Transport ? $value->value : $value)
                    ->all()
            )
        );
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
