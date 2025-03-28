<?php

namespace App\Models;

use App\Casts\PriceCast;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory,
        SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'duration',
        'image',
        'images',
        'active',
        'featured',
        'published_at',
    ];

    protected $appends = ['image_urls'];

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
            Storage::disk('public')->delete($product->getAttributes()['image']);
            $product->images()->delete();
            $product->itineraries()->delete();
        });

        static::restoring(function ($product) {
            $product->images()->withTrashed()->restore();
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

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function itineraries()
    {
        return $this->hasMany(Itinerary::class)->orderBy('order');
    }

    public function getRaw(string $value): float
    {
        return (float) $this->getRawOriginal($value);
    }

    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Storage::url($value)
        );
    }

    public function getImageUrlsAttribute(): Collection
    {
        return $this->images->map(fn ($image) => $image->path);
    }

    public function hasImage(): bool
    {
        return Storage::disk('public')->exists($this->getAttributes()['image']);
    }

    public function deleteImage(?string $path = null): void
    {
        Storage::disk('public')->delete($path ?? $this->getAttributes()['image']);
    }

    public function deleteImages(): void
    {
        foreach ($this->images as $image) {
            $path = $image->getAttributes()['path'];
            if ($this->hasImage($path)) {
                Storage::disk('public')->delete($path);
            }
        }
        $this->images()->delete();
    }
}
