<?php

namespace App\Models;

use App\Enums\Booking\CostCategory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $amount_per_person
 * @property int $quantity
 * @property int $sort_order
 * @property-read int $subtotal
 * @property-read Booking|null $booking
 */
class BookingCostItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'category',
        'label',
        'amount_per_person',
        'quantity',
        'sort_order',
    ];

    protected $casts = [
        'category' => CostCategory::class,
        'amount_per_person' => 'integer',
        'quantity' => 'integer',
        'sort_order' => 'integer',
    ];

    protected $appends = ['subtotal'];

    protected static function booted(): void
    {
        static::saved(fn (self $item) => $item->booking?->recalculatePrice());
        static::deleted(fn (self $item) => $item->booking?->recalculatePrice());
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    protected function subtotal(): Attribute
    {
        return Attribute::get(fn () => $this->amount_per_person * $this->quantity);
    }
}
