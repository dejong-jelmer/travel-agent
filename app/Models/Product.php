<?php

namespace App\Models;

use App\Casts\PriceCast;
use App\Models\Traits\HasStoreableImages;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Collection;

class Product extends Model
{
    use HasFactory,
        SoftDeletes,
        HasStoreableImages;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'duration',
        'active',
        'featured',
        'published_at',
    ];

    protected $appends = [
        'image_urls',
        'raw_price',
        'countries_list',
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
            'active' => 'boolean',
            'featured' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        parent::boot();
        static::deleting(function ($product) {
            $product->images()->delete();
            $product->featuredImage()->delete();
            $product->itineraries()->delete();
        });

        static::restoring(function ($product) {
            $product->images()->withTrashed()->restore();
            $product->featuredImage()->withTrashed()->restore();
            $product->itineraries()->withTrashed()->restore();
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function countries(): BelongsToMany
    {
        return $this->belongsToMany(Country::class);
    }

    /**
     * Scope a query to only include featured products.
     */
    #[Scope]
    protected function featured(Builder $query): void
    {
        $query->where('featured', 1);
    }

    /**
     * Scope a query to only include active products.
     */
    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('active', 1);
    }

    public function getCountriesListAttribute(): string
    {
        $countries = $this->countries->pluck('name');

        if ($countries->count() > 1) {
            return $countries->slice(0, -1)->implode(', ').' & '.$countries->last();
        }

        return $countries->first() ?? '';
    }

    public function itineraries(): HasMany
    {
        return $this->hasMany(Itinerary::class)->orderBy('order');
    }

    public function getRawPriceAttribute(): float
    {
        return (float) $this->getRawOriginal('price');
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable')->where('featured', false);
    }

    public function featuredImage(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable')->where('featured', true);
    }

    public function getImageUrlsAttribute(): Collection
    {
        return $this->images->pluck('path');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
