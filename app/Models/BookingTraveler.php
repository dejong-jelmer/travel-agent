<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\TravelerType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class BookingTraveler extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'type',
        'first_name',
        'last_name',
        'birthdate',
        'nationality'
    ];

    protected $appends = [
        'full_name',
        'birthdate_formatted',
    ];

    protected $casts = [
        'type' => TravelerType::class,
        'birthdate' => 'date',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->first_name . ' ' . $this->last_name,
        );
    }

    protected function birthdateFormatted(): Attribute
    {
        return Attribute::get(
            fn() =>
            $this->birthdate
                ? $this->birthdate->isoFormat('DD-MM-YYYY')
                : null
        );
    }

}
