<?php

namespace Tests\Feature;

use App\Enums\SettingKey;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_returns_null_when_key_does_not_exist(): void
    {
        $this->assertNull(Setting::get(SettingKey::BookingSeasonEnd));
    }

    public function test_get_returns_custom_default_when_key_does_not_exist(): void
    {
        $this->assertSame('fallback', Setting::get(SettingKey::BookingSeasonEnd, 'fallback'));
    }

    public function test_get_returns_stored_value(): void
    {
        Setting::set(SettingKey::BookingSeasonEnd, '2026-12-31');

        $this->assertSame('2026-12-31', Setting::get(SettingKey::BookingSeasonEnd));
    }

    public function test_set_creates_new_record(): void
    {
        Setting::set(SettingKey::BookingSeasonEnd, '2026-12-31');

        $this->assertDatabaseHas('settings', [
            'key' => 'booking_season_end',
            'value' => '2026-12-31',
        ]);
    }

    public function test_set_updates_existing_record_without_creating_duplicate(): void
    {
        Setting::set(SettingKey::BookingSeasonEnd, '2026-06-30');
        Setting::set(SettingKey::BookingSeasonEnd, '2026-12-31');

        $this->assertDatabaseCount('settings', 1);
        $this->assertDatabaseHas('settings', [
            'key' => 'booking_season_end',
            'value' => '2026-12-31',
        ]);
    }

    public function test_set_null_deletes_the_record(): void
    {
        Setting::set(SettingKey::BookingSeasonEnd, '2026-12-31');
        Setting::set(SettingKey::BookingSeasonEnd, null);

        $this->assertDatabaseMissing('settings', ['key' => 'booking_season_end']);
    }

    public function test_get_returns_default_after_record_is_deleted(): void
    {
        Setting::set(SettingKey::BookingSeasonEnd, '2026-12-31');
        Setting::set(SettingKey::BookingSeasonEnd, null);

        $this->assertSame('fallback', Setting::get(SettingKey::BookingSeasonEnd, 'fallback'));
    }

    public function test_set_null_on_nonexistent_key_does_not_fail(): void
    {
        Setting::set(SettingKey::BookingSeasonEnd, null);

        $this->assertDatabaseMissing('settings', ['key' => 'booking_season_end']);
    }
}
