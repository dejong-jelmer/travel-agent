<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class ProductImage extends Model
{
    use HasFactory,
        SoftDeletes;

    protected $fillable = [
        'product_id',
        'path',
    ];

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($productImage) {
            Storage::disk('public')->delete($productImage->getAttributes()['path']);
        });
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    protected function path(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Storage::url($value)
        );
    }
}
