<?php

namespace App\Travel\Models;

use App\Models\Booking;
use App\Travel\Database\Factories\StayFactory;
use App\Travel\Exceptions\InvalidStayException;
use App\Travel\Validation\StayInvariants;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[UseFactory(StayFactory::class)]
class Stay extends Model
{
    /** @use HasFactory<StayFactory> */
    use HasFactory;

    protected $table = 'travel_stays';

    protected $fillable = [
        'booking_id',
        'accommodation_id',
        'arrival',
        'departure',
        'position',
    ];

    protected $casts = [
        'arrival' => 'date',
        'departure' => 'date',
        'position' => 'integer',
    ];

    protected static function booted(): void
    {
        // A safety net for writes that bypass the form requests.
        static::saving(function (self $stay) {
            $broken = StayInvariants::check($stay);

            if ($broken !== []) {
                throw InvalidStayException::for($stay, $broken);
            }
        });
    }

    /**
     * @return BelongsTo<Booking, $this>
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * @return BelongsTo<Accommodation, $this>
     */
    public function accommodation(): BelongsTo
    {
        return $this->belongsTo(Accommodation::class);
    }

    /**
     * @return Attribute<int|null, never>
     */
    protected function nights(): Attribute
    {
        return Attribute::make(get: fn (): ?int => $this->arrival && $this->departure
            ? (int) $this->arrival->diffInDays($this->departure)
            : null);
    }
}
