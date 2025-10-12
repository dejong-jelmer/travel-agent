<?php

namespace App\Models;

use App\Enums\TravelerType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class BookingTraveler extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'type',
        'first_name',
        'last_name',
        'birthdate',
        'nationality',
    ];

    protected $appends = [
        'full_name',
        'birthdate_formatted',
    ];

    protected $casts = [
        'type' => TravelerType::class,
        'birthdate' => 'date',
    ];

    protected static function booted()
    {
        static::updated(function ($traveler) {
            $changes = $traveler->getChanges();
            $original = $traveler->getOriginal();

            unset($changes['updated_at'], $changes['created_at']);

            foreach ($changes as $field => $newValue) {
                BookingChange::create([
                    'booking_id' => $traveler->booking_id,
                    'user_id' => Auth::user()->id ?? null,
                    'model_type' => self::class,
                    'model_id' => $traveler->id,
                    'field' => $field,
                    'old_value' => $original[$field] ?? null,
                    'new_value' => $newValue,
                ]);
            }
        });
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->first_name.' '.$this->last_name,
        );
    }

    protected function birthdateFormatted(): Attribute
    {
        return Attribute::get(
            fn () => $this->birthdate
                ? $this->birthdate->isoFormat('DD-MM-YYYY')
                : null
        );
    }
}
