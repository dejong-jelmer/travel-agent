<?php

namespace App\Travel\Models;

use App\Travel\Database\Factories\StationFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[UseFactory(StationFactory::class)]
class Station extends Model
{
    /** @use HasFactory<StationFactory> */
    use HasFactory;

    protected $table = 'travel_stations';

    protected $fillable = [
        'city_id',
        'name',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    /**
     * @return BelongsTo<City, $this>
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}
