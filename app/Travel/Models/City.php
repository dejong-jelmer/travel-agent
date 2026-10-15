<?php

namespace App\Travel\Models;

use App\Travel\Database\Factories\CityFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[UseFactory(CityFactory::class)]
class City extends Model
{
    /** @use HasFactory<CityFactory> */
    use HasFactory;

    protected $table = 'travel_cities';

    protected $fillable = ['name', 'country'];

    /**
     * @return HasMany<Station, $this>
     */
    public function stations(): HasMany
    {
        return $this->hasMany(Station::class);
    }

    /**
     * @return HasMany<Accommodation, $this>
     */
    public function accommodations(): HasMany
    {
        return $this->hasMany(Accommodation::class);
    }

    /**
     * @return HasMany<Pharmacy, $this>
     */
    public function pharmacies(): HasMany
    {
        return $this->hasMany(Pharmacy::class);
    }
}
