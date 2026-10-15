<?php

namespace App\Travel\Models;

use App\Travel\Database\Factories\TransitLineFactory;
use App\Travel\Enums\TransitMode;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property-read object{direction: string|null, position: int} $pivot Only set when the line is loaded through a leg.
 */
#[UseFactory(TransitLineFactory::class)]
class TransitLine extends Model
{
    /** @use HasFactory<TransitLineFactory> */
    use HasFactory;

    protected $table = 'travel_transit_lines';

    protected $fillable = [
        'mode',
        'name',
        'color',
        'info_link',
    ];

    protected $casts = [
        'mode' => TransitMode::class,
    ];

    /**
     * @return BelongsToMany<Leg, $this>
     */
    public function legs(): BelongsToMany
    {
        return $this->belongsToMany(Leg::class, 'travel_leg_transit_line')
            ->withPivot('direction', 'position')
            ->orderByPivot('position');
    }
}
