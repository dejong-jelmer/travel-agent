<?php
namespace App\Models;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

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
        'category_id',
        'country_id',
    ];

    protected $appends = ['image_urls'];

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

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Storage::url($value)
        );
    }

    public function getImageUrlsAttribute(): Collection
    {
        return $this->images->map(fn($image) => $image->path);
    }

    public function deleteImage(): void
    {
        Storage::disk('public')->delete($this->getAttributes()['image']);

    }

    public function deleteImages(): void
    {
        foreach ($this->images as $image) {
            Storage::disk('public')->delete($image->getAttributes()['path']);
        }
        $this->images()->delete();
    }
}
