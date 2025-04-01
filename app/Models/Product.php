<?php

namespace App\Models;

use App\Casts\PriceCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\StoreableImage;

class Product extends Model
{
    use HasFactory,
        StoreableImage,
        SoftDeletes;

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

    public function country(): Country
    {
        return $this->countries()->first();
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
