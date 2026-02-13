<?php

namespace App\Models;

use App\Enums\Destination\TravelInfo;
use App\Models\Traits\Sortable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Destination extends Model
{
    use HasFactory,
        SoftDeletes,
        Sortable;

    protected $perPage = 15;

    protected $fillable = [
        'country_code',
        'name',
        'region',
        'travel_info',
    ];

    protected $casts = [
        'travel_info' => 'array',
    ];

    protected $appends = [];

    protected $searchable = ['country_code', 'name', 'region'];

    protected $sortable = ['id', 'country_code', 'name', 'region'];

    public function trips(): BelongsToMany
    {
        return $this->belongsToMany(Trip::class);
    }

    public function travelInfo(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                $rawValue = $value;

                // First check if this destination has travel_info
                if (is_null($rawValue)) {
                    // Or retrieve travel_info from country by country_code (region = null)
                    $rawValue = static::where('country_code', $this->country_code)
                        ->whereNull('region')
                        ->value('travel_info');
                }
                $decoded = match (true) {
                    is_null($rawValue), $rawValue === '' => [],
                    is_array($rawValue) => $rawValue,
                    default => json_decode($rawValue, true) ?? []
                };

                // Get all keys from TravelInfo enum
                $allKeys = collect(TravelInfo::cases())
                    ->mapWithKeys(fn ($case) => [$case->value => ''])
                    ->all();

                // Merge with existing values
                return array_merge($allKeys, $decoded);
            }
        );
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_code', 'code');
    }
}
