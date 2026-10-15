<?php

namespace App\Travel\Models;

use App\Models\Booking;
use App\Travel\Database\Factories\ShareLinkFactory;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * The link that shows the travelers their documents without signing in. A booking has at most one; a new one replaces
 * it. It is found by its token, and works until DAYS_AFTER_RETURN days after the booking's return date, so a later
 * return date extends it.
 */
#[UseFactory(ShareLinkFactory::class)]
class ShareLink extends Model
{
    /** @use HasFactory<ShareLinkFactory> */
    use HasFactory;

    public const DAYS_AFTER_RETURN = 30;

    protected $table = 'travel_share_links';

    protected $fillable = [
        'booking_id',
        'token',
    ];

    public function getRouteKeyName(): string
    {
        return 'token';
    }

    /**
     * @return BelongsTo<Booking, $this>
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * The last day the link works.
     */
    public function expiresOn(): CarbonInterface
    {
        return $this->booking->return_date->copy()->addDays(self::DAYS_AFTER_RETURN);
    }

    public function isExpired(): bool
    {
        return now()->startOfDay()->gt($this->expiresOn());
    }
}
