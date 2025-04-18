<?php

namespace App\Models;

use App\Casts\PriceCast;
use App\Traits\StoreableImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class Product extends Model
{
    use HasFactory,
        SoftDeletes,
        StoreableImage;

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

    protected static function booted()
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

    public function getCountriesListAttribute(): string
    {
        $countries = $this->countries->pluck('name');

        if ($countries->count() > 1) {
            return $countries->slice(0, -1)->implode(', ').' & '.$countries->last();
        }

        return $countries->first() ?? '';
    }

    public function itineraries()
    {
        return $this->hasMany(Itinerary::class)->orderBy('order');
    }

    public function getRawPriceAttribute(): float
    {
        return (float) $this->getRawOriginal('price');
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable')->where('featured', false);
    }

    public function featuredImage()
    {
        return $this->morphOne(Image::class, 'imageable')->where('featured', true);
    }

    public function getImageUrlsAttribute(): Collection
    {
        return $this->images->pluck('path');
    }
}
