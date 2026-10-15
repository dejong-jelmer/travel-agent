<?php

namespace App\Travel\Tests\Feature;

use App\Models\Booking;
use App\Travel\Actions\CreateJourney;
use App\Travel\Actions\CreateLeg;
use App\Travel\Actions\CreateStay;
use App\Travel\Actions\DeleteLeg;
use App\Travel\Enums\LegType;
use App\Travel\Models\Accommodation;
use App\Travel\Models\Journey;
use App\Travel\Models\Leg;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PositionTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_new_leg_follows_the_highest_position_of_its_own_journey(): void
    {
        $journey = Journey::factory()->create();
        Leg::factory()->walk()->for($journey, 'legable')->create(['position' => 0]);
        Leg::factory()->walk()->for($journey, 'legable')->create(['position' => 4]);
        // Higher positions elsewhere do not count: another journey, and an arrival route with the same legable_id.
        Leg::factory()->walk()->create(['position' => 9]);
        $route = Accommodation::factory()->create(['id' => $journey->id]);
        Leg::factory()->walk()->for($route, 'legable')->create(['position' => 7]);

        $leg = app(CreateLeg::class)->handle($journey, ['type' => LegType::Walk]);

        $this->assertSame(5, $leg->position);
    }

    public function test_the_first_leg_of_an_arrival_route_gets_position_0(): void
    {
        $leg = app(CreateLeg::class)->handle(Accommodation::factory()->create(), ['type' => LegType::Walk]);

        $this->assertSame(0, $leg->position);
    }

    public function test_a_new_journey_and_stay_follow_the_highest_position_of_their_own_booking(): void
    {
        $booking = Booking::factory()->create();
        Journey::factory()->for($booking)->create(['position' => 2]);
        Journey::factory()->create(['position' => 8]);

        $this->assertSame(3, app(CreateJourney::class)->handle($booking, ['label' => 'Terugreis'])->position);

        $accommodation = Accommodation::factory()->create();
        $this->assertSame(0, app(CreateStay::class)->handle($booking, ['accommodation_id' => $accommodation->id])->position);
        $this->assertSame(1, app(CreateStay::class)->handle($booking, ['accommodation_id' => $accommodation->id])->position);
    }

    public function test_deleting_leaves_the_gap(): void
    {
        $journey = Journey::factory()->create();
        $legs = collect(range(1, 3))->map(fn () => app(CreateLeg::class)->handle($journey, ['type' => LegType::Walk]));

        app(DeleteLeg::class)->handle($legs[1]);

        $this->assertSame([0, 2], $journey->legs()->pluck('position')->all());
        $this->assertSame(3, app(CreateLeg::class)->handle($journey, ['type' => LegType::Walk])->position);
    }
}
