<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    use HasFactory,
        SoftDeletes;

    protected $fillable = [
        'path',
        'is_primary',
        'original_name',
        'mime_type',
        'size',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    protected $appends = [
        'public_url',
        'full_path',
    ];

    public function imageable()
    {
        return $this->morphTo();
    }

    /**
     * Get the full storage path for the image.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, never>
     */
    public function fullPath(): Attribute
    {
        return Attribute::get(function () {
            return config('images.directory') . "/{$this->path}";
        });
    }

    /**
     * Get the public URL for the image.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, never>
     */
    public function publicUrl(): Attribute
    {
        return Attribute::get(fn() => url(Storage::url($this->full_path)));
    }
}
