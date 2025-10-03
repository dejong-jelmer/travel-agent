<?php

namespace App\Models;

use App\Models\Booking;
use App\Models\BookingChange;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class BookingContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'name',
        'street',
        'house_number',
        'addition',
        'postal_code',
        'city',
        'email',
        'phone',
        'country',
    ];

    protected $appends = [
        'address',
    ];

    protected static function booted()
    {
        static::updated(function ($contact) {
            $changes = $contact->getChanges();
            $original = $contact->getOriginal();

            unset($changes['updated_at'], $changes['created_at']);

            foreach ($changes as $field => $newValue) {
                BookingChange::create([
                    'booking_id' => $contact->booking_id,
                    'user_id' => Auth::user()->id ?? null,
                    'model_type' => self::class,
                    'model_id' => $contact->id,
                    'field' => $field,
                    'old_value' => $original[$field] ?? null,
                    'new_value' => $newValue,
                ]);
            }
        });
    }

    protected function address(): Attribute
    {
        return Attribute::get(
            fn () => "{$this->street} {$this->house_number} {$this->addition}\n{$this->postal_code} {$this->city}"
        );
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
