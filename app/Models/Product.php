<?php
namespace App\Models;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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

    public function country(): Country
    {
        return $this->countries()->first();
    }

    public function countries(): BelongsToMany
    {
        return $this->belongsToMany(Country::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function itineraries()
    {
        return $this->hasMany(Itinerary::class)->orderBy('order');
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
