<?php

namespace App\Travel\Tests\Feature;

use App\Models\Booking;
use App\Travel\Actions\Reorder;
use App\Travel\Models\Journey;
use App\Travel\Models\Leg;
use App\Travel\Models\Stay;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ReorderTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_rewrites_the_positions_of_the_legs_in_the_given_order(): void
    {
        $journey = Journey::factory()->create();
        [$first, $second, $third] = $this->legsOf($journey);

        app(Reorder::class)->handle($journey->legs(), [$third->id, $first->id, $second->id]);

        $this->assertSame([$third->id, $first->id, $second->id], $journey->legs()->pluck('id')->all());
        $this->assertSame([0, 1, 2], $journey->legs()->pluck('position')->all());
    }

    public function test_it_reorders_the_journeys_and_the_stays_of_a_booking(): void
    {
        $booking = Booking::factory()->create();
        $journeys = Journey::factory()->for($booking)->count(2)->sequence(['position' => 0], ['position' => 1])->create();
        $stays = Stay::factory()->for($booking)->count(2)->sequence(['position' => 0], ['position' => 1])->create();

        app(Reorder::class)->handle($booking->journeys(), [$journeys[1]->id, $journeys[0]->id]);
        app(Reorder::class)->handle($booking->stays(), [$stays[1]->id, $stays[0]->id]);

        $this->assertSame([$journeys[1]->id, $journeys[0]->id], $booking->journeys()->pluck('id')->all());
        $this->assertSame([$stays[1]->id, $stays[0]->id], $booking->stays()->pluck('id')->all());
    }

    public function test_it_refuses_ids_that_are_not_exactly_the_children(): void
    {
        $journey = Journey::factory()->create();
        [$first, $second, $third] = $this->legsOf($journey);
        $otherLeg = Leg::factory()->walk()->create();

        foreach ([
            'partial' => [$second->id, $first->id],
            'foreign' => [$third->id, $second->id, $otherLeg->id],
            'twice' => [$first->id, $first->id, $second->id],
            'extra' => [$third->id, $second->id, $first->id, $otherLeg->id],
        ] as $ids) {
            $this->assertThrows(fn () => app(Reorder::class)->handle($journey->legs(), $ids), ValidationException::class);
        }

        $this->assertSame([$first->id, $second->id, $third->id], $journey->legs()->pluck('id')->all());
        $this->assertSame([0, 1, 2], $journey->legs()->pluck('position')->all());
    }

    /**
     * @return list<Leg>
     */
    private function legsOf(Journey $journey): array
    {
        return Leg::factory()->walk()->for($journey, 'legable')->count(3)
            ->sequence(fn ($sequence) => ['position' => $sequence->index])
            ->create()
            ->all();
    }
}
