<?php

namespace App\Travel\Tests\Feature;

use App\Travel\Models\Leg;
use Tests\TestCase;

class LegDurationTest extends TestCase
{
    public function test_the_duration_follows_from_the_times(): void
    {
        $leg = new Leg(['departure_at' => '2026-08-11 05:43', 'arrival_at' => '2026-08-11 06:37']);

        $this->assertSame(54, $leg->duration);
    }

    public function test_without_both_times_the_stored_duration_is_used(): void
    {
        $this->assertSame(11, (new Leg(['duration_minutes' => 11]))->duration);
        $this->assertSame(102, (new Leg(['departure_at' => '2026-08-11 11:05', 'duration_minutes' => 102]))->duration);
    }

    public function test_without_times_or_a_stored_duration_there_is_none(): void
    {
        $this->assertNull((new Leg)->duration);
    }
}
