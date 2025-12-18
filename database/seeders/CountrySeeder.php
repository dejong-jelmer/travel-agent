<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Resources\CountryResource;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Seed European countries for sustainable train travel
     */
    public function run(): void
    {
        foreach (CountryResource::names() as $countryName) {
            Country::firstOrCreate(['name' => $countryName]);
        }
    }
}
