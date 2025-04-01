<?php

namespace App\Models;

use App\Casts\PathCast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Image extends Model
{
    use HasFactory,
        SoftDeletes;

    protected $fillable = ['path', 'featured'];

    protected $casts = [
        'path' => PathCast::class,
        'featured' => 'boolean',
    ];

    public function imageable()
    {
        return $this->morphTo();
    }
}
