<?php

namespace Tests\Unit\Enums;

use App\Enums\Booking\Status;
use PHPUnit\Framework\TestCase;

class BookingStatusTest extends TestCase
{
    public function test_every_status_but_canceled_occupies_a_seat(): void
    {
        $this->assertSame(
            [Status::New, Status::Pending, Status::Confirmed, Status::Completed],
            Status::seatOccupying()
        );
        $this->assertFalse(Status::Canceled->occupiesSeat());
    }

    public function test_only_confirmed_and_completed_count_towards_the_minimum(): void
    {
        $this->assertSame([Status::Confirmed, Status::Completed], Status::minimumCounting());
        $this->assertFalse(Status::New->countsTowardsMinimum());
        $this->assertFalse(Status::Pending->countsTowardsMinimum());
        $this->assertFalse(Status::Canceled->countsTowardsMinimum());
    }

    public function test_every_status_that_counts_towards_the_minimum_also_occupies_a_seat(): void
    {
        foreach (Status::minimumCounting() as $status) {
            $this->assertTrue($status->occupiesSeat());
        }
    }
}
