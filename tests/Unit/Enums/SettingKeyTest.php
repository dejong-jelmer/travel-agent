<?php

namespace Tests\Unit\Enums;

use App\Enums\SettingKey;
use PHPUnit\Framework\TestCase;

class SettingKeyTest extends TestCase
{
    public function test_booking_season_end_has_correct_string_value(): void
    {
        $this->assertSame('booking_season_end', SettingKey::BookingSeasonEnd->value);
    }

    public function test_can_be_resolved_from_string_value(): void
    {
        $this->assertSame(SettingKey::BookingSeasonEnd, SettingKey::from('booking_season_end'));
    }

    public function test_from_unknown_value_throws_exception(): void
    {
        $this->expectException(\ValueError::class);

        SettingKey::from('unknown_key');
    }
}
