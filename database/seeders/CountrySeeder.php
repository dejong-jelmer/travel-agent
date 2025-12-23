<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Services\CountryService;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Seed European countries for sustainable train travel
     */
    public function run(): void
    {
        foreach (CountryService::names() as $countryName) {
            Country::firstOrCreate(['name' => $countryName]);
        }
    }
}
