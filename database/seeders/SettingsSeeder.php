<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's default settings.
     */
    public function run(): void
    {
        Setting::factory()->bookingFee()->create();
        Setting::factory()->guaranteeFund()->create();
        Setting::factory()->bookingSeasonEnd()->create();
    }
}
