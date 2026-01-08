<?php

namespace App\Models;

use App\Casts\MealCast;
use App\Casts\TransportCast;
use App\Enums\Meal;
use App\Enums\Transport;
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

    protected $appends = [
        'meals_formatted',
        'transport_formatted',
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
                : '[]'
        );
    }

    /**
     * Get a formatted transport.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, never>
     */
    public function transportFormatted(): Attribute
    {
        return Attribute::get(fn () => $this->formatEnumAttribute('transport', Transport::class));
    }

    /**
     * Get a formatted meals.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, never>
     */
    public function mealsFormatted(): Attribute
    {
        return Attribute::get(fn () => $this->formatEnumAttribute('meals', Meal::class));
    }

    private function formatEnumAttribute(string $attribute, string $enumClass): array
    {
        $raw = $this->getRawOriginal($attribute);

        return collect(json_decode($raw ?? '[]', true))
            ->map(fn ($value) => [
                'value' => $value,
                'label' => $enumClass::from($value)->label(),
            ])
            ->all();
    }
}
