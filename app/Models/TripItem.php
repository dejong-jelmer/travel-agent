<?php

namespace App\Models;

use App\Enums\Trip\ItemCategory;
use App\Enums\Trip\ItemType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripItem extends Model
{
    protected $fillable = [
        'trip_id',
        'type',
        'category',
        'item',
    ];

    protected $casts = [
        'type' => ItemType::class,
        'category' => ItemCategory::class,
    ];

    protected $appends = [
        'type_label',
        'category_label',
    ];

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    protected function typeLabel(): Attribute
    {
        return Attribute::get(fn () => $this->type?->label());
    }

    protected function categoryLabel(): Attribute
    {
        return Attribute::get(fn () => $this->category?->label());
    }
}
