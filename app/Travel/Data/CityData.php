<?php

namespace App\Travel\Data;

use App\Travel\Models\City;
use App\Travel\Models\Pharmacy;

readonly class CityData
{
    /**
     * @param  list<PharmacyData>  $pharmacies
     */
    public function __construct(
        public string $name,
        public string $country,
        public array $pharmacies,
    ) {}

    public static function fromModel(City $city): self
    {
        return new self(
            name: $city->name,
            country: $city->country,
            pharmacies: $city->pharmacies->map(fn (Pharmacy $pharmacy) => PharmacyData::fromModel($pharmacy))->all(),
        );
    }
}
