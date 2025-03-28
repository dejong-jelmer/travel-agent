<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;

class Itinerary extends Model
{
    use HasFactory,
        SoftDeletes;

    protected $fillable = [
        'product_id',
        'title',
        'description',
        'subtitle',
        'image',
        'remark',
    ];

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($itinerary) {
            Storage::disk('public')->delete($itinerary->getAttributes()['image']);
        });
        static::deleted(fn ($itinerary) => $itinerary->reOrder());
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Storage::url($value)
        );
    }

    public function hasImage(): bool
    {
        return Storage::disk('public')->exists($this->getAttributes()['image']);
    }

    public function deleteImage(): void
    {
        Storage::disk('public')->delete($this->getAttributes()['image']);
    }

    public function reOrder(): void
    {
        $order = 1;
        $itineraries = static::where('product_id', $this->product_id)
            ->where('id', '!=', $this->id)
            ->orderBy('order')
            ->get();

        foreach ($itineraries as $itinerary) {
            $itinerary->order = $order++;
            $itinerary->save();
        }
    }
}
