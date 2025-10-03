<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class BookingChange extends Model
{
    protected $fillable = [
        'booking_id',
        'user_id',
        'model_type',
        'model_id',
        'field',
        'old_value',
        'new_value',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function model(): MorphTo
    {
        return $this->morphTo();
    }
}
