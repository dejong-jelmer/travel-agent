<?php

namespace App\Models;

use App\Enums\Trip\PriceLabel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TripPrice extends Model
{
    use HasFactory,
        SoftDeletes;

    protected $fillable = [
        'trip_id',
        'valid_from',
        'valid_until',
        'base_price_pp',
        'single_supplement',
        'label',
    ];

    protected $casts = [
        'label' => PriceLabel::class,
        'valid_from' => 'date',
        'valid_until' => 'date',
    ];

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }
}
