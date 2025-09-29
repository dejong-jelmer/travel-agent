<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'name',
        'street',
        'house_number',
        'addition',
        'postal',
        'city',
        'email',
        'phone',
        'country',
    ];

    protected $appends = [
        'address',
    ];

    protected function address(): Attribute
    {
        return Attribute::get(
            fn () => "{$this->street} {$this->house_number} {$this->addition}\n{$this->postal} {$this->city}"
        );
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
