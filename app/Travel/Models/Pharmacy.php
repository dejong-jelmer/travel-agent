<?php

namespace App\Travel\Models;

use App\Travel\Database\Factories\PharmacyFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[UseFactory(PharmacyFactory::class)]
class Pharmacy extends Model
{
    /** @use HasFactory<PharmacyFactory> */
    use HasFactory;

    protected $table = 'travel_pharmacies';

    protected $fillable = [
        'city_id',
        'name',
        'address',
    ];

    /**
     * @return BelongsTo<City, $this>
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}
