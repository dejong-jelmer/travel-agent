<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = file_get_contents(database_path('data/countries.json'));
        $data = json_decode($json, true);

        $countries = collect($data)
            ->map(function ($country) {
                return [
                    'code' => $country['cca2'],
                    'name' => $country['name']['common'],
                    'region' => $country['region'],
                    'translations' => json_encode($country['translations'] ?? []),
                ];
            })
            ->sortBy('name')
            ->values();

        DB::table('countries')->insert($countries->toArray());

        $this->command->info("Seeded {$countries->count()} countries");
    }
}
