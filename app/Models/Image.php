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
