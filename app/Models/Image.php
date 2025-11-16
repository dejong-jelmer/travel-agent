<?php

namespace App\Models;

use App\Casts\PathCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        'path' => PathCast::class,
        'featured' => 'boolean',
    ];

    protected $appends = [
        'raw_path',
    ];

    public function imageable()
    {
        return $this->morphTo();
    }

    public function getRawPathAttribute(): string
    {
        return $this->getRawOriginal('path');
    }
}
