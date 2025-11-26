<?php

namespace App\Models;

use App\Casts\PriceCast;
use App\Models\Traits\ManagesImages;
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
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Trip extends Model
{
    use HasFactory,
        ManagesImages,
        SoftDeletes;

    protected $perPage = 10;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'duration',
        'featured',
        'published_at',
        'meta_title',
        'meta_description',
    ];

    protected $appends = [
        'image_paths',
        'raw_price',
        'formatted_countries',
        'og_image_url',
    ];

    protected $casts = [
        'price' => PriceCast::class,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'date',
            'featured' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        parent::boot();
        static::deleting(function ($trip) {
            $trip->images()->delete();
            $trip->heroImage()->delete();
            $trip->itineraries()->delete();
        });

        static::restoring(function ($trip) {
            $trip->images()->withTrashed()->restore();
            $trip->heroImage()->withTrashed()->restore();
            $trip->itineraries()->withTrashed()->restore();
        });
    }

    public function countries(): BelongsToMany
    {
        return $this->belongsToMany(Country::class);
    }

    /**
     * Scope a query to only include featured trips.
     */
    #[Scope]
    protected function featured(Builder $query): void
    {
        $query->where('featured', 1);
    }

    /**
     * Scope a query to only include published trips.
     */
    #[Scope]
    protected function published(Builder $query): void
    {
        $query->where('published_at', '<=', today());
    }

    /**
     * Get a formatted, comma-separated list of country names.
     *
     * Multiple countries are joined with commas and an ampersand before the last item.
     * Example: "Netherlands, Belgium & Germany"
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, never>
     */
    public function formattedCountries(): Attribute
    {
        return Attribute::get(function () {
            $countries = $this->countries->pluck('name');

            return match ($countries->count()) {
                0 => '',
                1 => $countries->first(),
                default => $countries->slice(0, -1)->implode(', ').' & '.$countries->last()
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
    public function rawPrice(): Attribute
    {
        return Attribute::get(
            fn () => (float) $this->getRawOriginal('price')
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
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string|null, never>
     */
    public function ogImageUrl(): Attribute
    {
        return Attribute::get(
            fn () => $this->heroImage?->public_url
        );
    }

    /**
     * Get the meta_description property or fallback to substring of trip->description.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, never>
     */
    public function metaDescription(): Attribute
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
     * Get the meta_titleproperty or fallback to substring of trip->name.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, never>
     */
    public function metaTitle(): Attribute
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
}
