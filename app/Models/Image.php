<?php

namespace App\Models;

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
        'featured',
        'original_name',
        'mime_type',
        'size',
    ];

    protected $casts = [
        'featured' => 'boolean',
    ];

    protected $appends = [
        'full_path',
    ];

    public function imageable()
    {
        return $this->morphTo();
    }

    public function getFullPathAttribute(): string
    {
        $dir = config('images.directory');

        return Storage::url("{$dir}/{$this->path}");
    }
}
