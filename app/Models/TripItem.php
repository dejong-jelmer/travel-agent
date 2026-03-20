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
        'is_inclusive',
        'is_exclusive',
    ];

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    protected function isInclusive(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->type === ItemType::Inclusion,
        );
    }

    protected function isExclusive(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->type === ItemType::Exclusion,
        );
    }
}
