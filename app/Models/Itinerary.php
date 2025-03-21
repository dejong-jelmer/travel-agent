<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Itinerary extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'title',
        'description',
        'sub',
        'image',
        'remark',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
