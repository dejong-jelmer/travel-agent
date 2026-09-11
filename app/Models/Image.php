<?php

namespace App\Models;

use App\Services\ImageVariantService;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

/**
 * @property string $public_url
 * @property string $full_path
 * @property array<int, array{width: int, height: int, size: int}>|null $variants
 * @property array<int, array{url: string, width: int, height: int}> $sources
 * @property array{url: string, width: int, height: int}|null $fallback_source
 */
class Image extends Model
{
    use HasFactory,
        SoftDeletes;

    protected $fillable = [
        'path',
        'is_primary',
        'order',
        'original_name',
        'mime_type',
        'size',
        'width',
        'height',
        'variants',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'order' => 'integer',
        'variants' => 'array',
    ];

    protected $appends = [
        'public_url',
        'full_path',
        'sources',
        'fallback_source',
    ];

    public function imageable()
    {
        return $this->morphTo();
    }

    /**
     * Get the full storage path for the image.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<non-falsy-string, never>
     */
    public function fullPath(): Attribute
    {
        return Attribute::get(function () {
            return config('images.directory')."/{$this->path}";
        });
    }

    /**
     * Get the public URL for the image.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, never>
     */
    public function publicUrl(): Attribute
    {
        return Attribute::get(fn () => url(Storage::url($this->full_path)));
    }

    /**
     * Get the URL and dimensions of the WebP variants, from narrow to wide.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<array<int, array{url: string, width: int, height: int}>, never>
     */
    public function sources(): Attribute
    {
        return Attribute::get(fn () => app(ImageVariantService::class)->sources($this));
    }

    /**
     * Get the variant used as img src for browsers that ignore srcset, if the image has variants.
     */
    public function fallbackSource(): Attribute
    {
        return Attribute::get(fn () => app(ImageVariantService::class)->fallback($this));
    }
}
