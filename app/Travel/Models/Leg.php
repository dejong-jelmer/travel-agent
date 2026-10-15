<?php

namespace App\Travel\Models;

use App\Travel\Database\Factories\LegFactory;
use App\Travel\Enums\LegType;
use App\Travel\Exceptions\InvalidLegException;
use App\Travel\Validation\LegInvariants;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[UseFactory(LegFactory::class)]
class Leg extends Model
{
    /** @use HasFactory<LegFactory> */
    use HasFactory;

    protected $table = 'travel_legs';

    protected $fillable = [
        'type',
        'from_station_id',
        'to_station_id',
        'departure_at',
        'arrival_at',
        'duration_minutes',
        'number',
        'carriage',
        'seat',
        'note',
        'position',
    ];

    protected $casts = [
        'type' => LegType::class,
        'from_station_id' => 'integer',
        'to_station_id' => 'integer',
        'departure_at' => 'datetime',
        'arrival_at' => 'datetime',
        'duration_minutes' => 'integer',
        'position' => 'integer',
    ];

    protected static function booted(): void
    {
        // A safety net for writes that bypass the form requests. The transit line invariants live in the action
        // that syncs them, as attach() and sync() fire no model events.
        static::saving(function (self $leg) {
            $broken = LegInvariants::check($leg);

            if ($broken !== []) {
                throw InvalidLegException::for($leg, $broken);
            }
        });
    }

    /**
     * The duration in minutes: from the times when both are set, otherwise the stored duration_minutes, which
     * transfers and metro legs carry without times. Render this, never duration_minutes.
     *
     * @return Attribute<int|null, never>
     */
    protected function duration(): Attribute
    {
        return Attribute::make(get: fn (): ?int => $this->departure_at && $this->arrival_at
            ? (int) $this->departure_at->diffInMinutes($this->arrival_at)
            : $this->duration_minutes);
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function legable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return BelongsToMany<TransitLine, $this>
     */
    public function transitLines(): BelongsToMany
    {
        return $this->belongsToMany(TransitLine::class, 'travel_leg_transit_line')
            ->withPivot('direction', 'position')
            ->orderByPivot('position');
    }

    /**
     * @return BelongsTo<Station, $this>
     */
    public function fromStation(): BelongsTo
    {
        return $this->belongsTo(Station::class, 'from_station_id');
    }

    /**
     * @return BelongsTo<Station, $this>
     */
    public function toStation(): BelongsTo
    {
        return $this->belongsTo(Station::class, 'to_station_id');
    }
}
