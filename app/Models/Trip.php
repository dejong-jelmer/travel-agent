<?php

namespace App\Models;

use App\Enums\Transport;
use App\Enums\Trip\PracticalInfo;
use App\Models\Traits\HasFormattedDates;
use App\Models\Traits\ManagesImages;
use App\Models\Traits\Sortable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * @property string $name
 * @property string $og_image_url
 * @property \Illuminate\Support\Collection $image_paths
 * @property string $destinations_formatted
 * @property Image|null $heroImage
 * @property array $transport_formatted
 */
class Trip extends Model
{
    use HasFactory,
        HasFormattedDates,
        ManagesImages,
        SoftDeletes,
        Sortable;

    protected $perPage = 10;

    protected array $formattedDates = [
        'published_at' => ['format' => 'dddd LL'],
    ];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'duration',
        'transport',
        'featured',
        'published_at',
        'highlights',
        'practical_info',
        'blocked_dates',
        'meta_title',
        'meta_description',
    ];

    protected $appends = [
        'image_paths',
        'price_formatted',
        'destinations_formatted',
        'published_at_formatted',
        'og_image_url',
        'transport_formatted',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'transport' => 'array',
        'highlights' => 'array',
        'practical_info' => 'array',
        'blocked_dates' => 'array',
        'published_at' => 'datetime',
        'featured' => 'boolean',
    ];

    // Sortable properties
    protected $searchable = ['name'];

    protected $searchableRelations = ['destinations.name'];

    protected $sortable = ['id', 'name', 'price', 'duration', 'published_at', 'destinations'];

    protected $sortableBelongsToMany = [
        'destinations' => [
            'relation' => 'destinations',
            'column' => 'name',
            'pivot_table' => 'destination_trip',
            'pivot_foreign_key' => 'trip_id',
            'pivot_related_key' => 'destination_id',
        ],
    ];

    protected static function booted(): void
    {
        parent::boot();
        static::deleting(function ($trip) {
            $trip->images()->delete();
            $trip->heroImage()->delete();
            $trip->itineraries()->delete();
            $trip->items()->delete();
        });

        static::restoring(function ($trip) {
            $trip->images()->withTrashed()->restore();
            $trip->heroImage()->withTrashed()->restore();
            $trip->itineraries()->withTrashed()->restore();
        });
    }

    public function destinations(): BelongsToMany
    {
        return $this->belongsToMany(Destination::class)->withTimestamps();
    }

    public function items(): HasMany
    {
        return $this->hasMany(TripItem::class);
    }

    /**
     * Scope a query to only include featured trips.
     */
    #[Scope]
    protected function featured(Builder $query): void
    {
        $query->where('featured', true);
    }

    /**
     * Scope a query to only include published trips.
     */
    #[Scope]
    protected function published(Builder $query): void
    {
        $query->where('published_at', '<=', today());
    }

    protected function publishedAtFormatted(): Attribute
    {
        return Attribute::get(fn () => $this->getFormattedDate('published_at'));
    }

    /**
     * Get a formatted, comma-separated list of destination names.
     *
     * Multiple destinations are joined with commas and an ampersand before the last item.
     * Example: "Netherlands, Belgium & Germany"
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, never>
     */
    public function destinationsFormatted(): Attribute
    {
        return Attribute::get(function () {
            /** @var \Illuminate\Database\Eloquent\Collection<int, Destination> $destinations */
            $destinations = $this->destinations;
            $names = $destinations->map(fn (Destination $d) => $d->region ?? $d->name);

            return match ($names->count()) {
                0 => '',
                1 => $names->first(),
                default => $names->slice(0, -1)->implode(', ').' & '.$names->last()
            };
        });
    }

    public function itineraries(): HasMany
    {
        return $this->hasMany(Itinerary::class)->orderBy('order');
    }

    /**
     * Get the raw database value for the price attribute .
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, never>
     */
    protected function priceFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => number_format((float) $this->price, 0, ',', '.')
        );
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable')->where('is_primary', false);
    }

    public function heroImage(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable')->where('is_primary', true);
    }

    /**
     * Get a collection of image paths associated with this trip.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<\Illuminate\Support\Collection, never>
     */
    public function imagePaths(): Attribute
    {
        return Attribute::get(fn () => $this->images->pluck('path'));
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get the hero image URL for Open Graph usage.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, never>
     */
    public function ogImageUrl(): Attribute
    {
        return Attribute::get(
            fn () => $this->heroImage?->public_url ?? asset(config('seo.default_og_image', 'images/og_image.jpg')) // @phpstan-ignore nullsafe.neverNull
        );
    }

    /**
     * Get the meta_description property or fallback to substring of $trip->description.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, never>
     */
    protected function metaDescription(): Attribute
    {
        return Attribute::get(
            fn (?string $value) => $value ?? Str::substr(
                $this->description ?? '',
                0,
                config(
                    'seo.meta_description_max_length'
                )
            )
        );
    }

    /**
     * Get the meta_title property or fallback to substring of $trip->name.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, never>
     */
    protected function metaTitle(): Attribute
    {
        return Attribute::get(
            fn (?string $value) => $value ?? Str::substr(
                $this->name ?? '',
                0,
                config(
                    'seo.meta_title_max_length'
                )
            )
        );
    }

    /**
     * Get transport modes with translated labels.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<array, never>
     */
    public function transportFormatted(): Attribute
    {
        return Attribute::get(
            fn () => collect($this->transport ?? [])
                ->map(fn (string $value) => [
                    'value' => $value,
                    'label' => Transport::from($value)->label(),
                ])
                ->toArray()
        );
    }

    /**
     * Get the trip highlights
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, never>
     */
    protected function highlights(): Attribute
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

    /**
     * Get the practical info with all keys from PracticalInfo enum
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<array, never>
     */
    protected function practicalInfo(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                $decoded = is_null($value) ? [] : json_decode($value, true);

                // Get all keys from PracticalInfo enum
                $allKeys = collect(PracticalInfo::cases())
                    ->mapWithKeys(fn ($case) => [$case->value => ''])
                    ->all();

                // Merge with existing values
                return array_merge($allKeys, $decoded);
            },
            set: fn ($value) => json_encode($value ?? [])
        );
    }
}
