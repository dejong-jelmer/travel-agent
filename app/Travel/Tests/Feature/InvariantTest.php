<?php

namespace App\Travel\Tests\Feature;

use App\Travel\Actions\SyncLegTransitLines;
use App\Travel\Enums\LegType;
use App\Travel\Enums\TransitMode;
use App\Travel\Exceptions\InvalidLegException;
use App\Travel\Exceptions\InvalidStayException;
use App\Travel\Models\Leg;
use App\Travel\Models\Station;
use App\Travel\Models\Stay;
use App\Travel\Models\TransitLine;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Every invariant, refused and accepted. Invariants 1, 4, 5, 6 and 7 are guarded by the models on every save; 2 and 3
 * by the action that syncs the transit lines, as attach() and sync() fire no model events.
 */
class InvariantTest extends TestCase
{
    use RefreshDatabase;

    public function test_1_a_transfer_starts_and_ends_at_one_station(): void
    {
        $this->assertThrows(
            fn () => Leg::factory()->transfer()->create(['to_station_id' => Station::factory()]),
            InvalidLegException::class,
            'TransferStaysInOnePlace',
        );
        $this->assertThrows(
            fn () => Leg::factory()->transfer()->create(['from_station_id' => null, 'to_station_id' => null]),
            InvalidLegException::class,
            'TransferStaysInOnePlace',
        );

        $this->assertModelExists(Leg::factory()->transfer()->create());
    }

    public function test_2_a_transfer_or_walk_carries_no_lines(): void
    {
        $line = TransitLine::factory()->create();

        foreach ([Leg::factory()->transfer()->create(), Leg::factory()->walk()->create()] as $leg) {
            $this->assertThrows(
                fn () => app(SyncLegTransitLines::class)->handle($leg, [[$line->id, null]]),
                InvalidLegException::class,
                'OnlyTransitCarriesLines',
            );
        }
        $this->assertDatabaseCount('travel_leg_transit_line', 0);

        $train = Leg::factory()->train()->create();
        app(SyncLegTransitLines::class)->handle($train, [[$line->id, 'Den Helder']]);
        $this->assertDatabaseHas('travel_leg_transit_line', ['leg_id' => $train->id, 'transit_line_id' => $line->id]);
    }

    public function test_3_the_lines_of_a_leg_match_its_type(): void
    {
        $tramLeg = Leg::factory()->create(['type' => LegType::Tram]);
        $tramLine = TransitLine::factory()->create(['mode' => TransitMode::Tram]);
        $metroLine = TransitLine::factory()->create(['mode' => TransitMode::Metro]);

        $this->assertThrows(
            fn () => app(SyncLegTransitLines::class)->handle($tramLeg, [[$tramLine->id, null], [$metroLine->id, null]]),
            InvalidLegException::class,
            'LinesMatchTheLeg',
        );
        $this->assertDatabaseCount('travel_leg_transit_line', 0);

        app(SyncLegTransitLines::class)->handle($tramLeg, [[$tramLine->id, 'Hippodrome']]);
        $this->assertDatabaseHas('travel_leg_transit_line', ['leg_id' => $tramLeg->id, 'transit_line_id' => $tramLine->id]);
    }

    public function test_the_lines_of_a_leg_exist_or_nothing_changes(): void
    {
        $train = Leg::factory()->train()->create();
        $line = TransitLine::factory()->create();
        app(SyncLegTransitLines::class)->handle($train, [[$line->id, 'Den Helder']]);

        $this->assertThrows(
            fn () => app(SyncLegTransitLines::class)->handle($train, [[$line->id, 'Amsterdam'], [$line->id + 1000, null]]),
            ModelNotFoundException::class,
        );
        $this->assertSame(['Den Helder'], $train->transitLines()->pluck('direction')->all());
    }

    /**
     * A race cannot be played out in one process, so this checks what the guarantee rests on: the mode of the lines is
     * read with a shared lock, so a change of mode waits for the sync, or the sync for the change.
     */
    public function test_the_lines_are_read_with_a_shared_lock(): void
    {
        $train = Leg::factory()->train()->create();
        $line = TransitLine::factory()->create();

        DB::enableQueryLog();
        app(SyncLegTransitLines::class)->handle($train, [[$line->id, 'Den Helder']]);
        $read = collect(DB::getQueryLog())->pluck('query')->first(fn (string $query) => str_contains($query, 'from `travel_transit_lines`'));

        $this->assertMatchesRegularExpression('/ lock in share mode$/', (string) $read);
    }

    public function test_4_an_arrival_route_has_no_times(): void
    {
        $this->assertThrows(
            fn () => Leg::factory()->accommodationRoute()->create(['departure_at' => '2026-08-14 09:00']),
            InvalidLegException::class,
            'ArrivalRouteHasNoClock',
        );
        $this->assertThrows(
            fn () => Leg::factory()->accommodationRoute()->create(['arrival_at' => '2026-08-14 09:10']),
            InvalidLegException::class,
            'ArrivalRouteHasNoClock',
        );

        $this->assertModelExists(Leg::factory()->walk()->accommodationRoute()->create());
        $this->assertModelExists(Leg::factory()->train()->create());
    }

    public function test_5_time_runs_forward(): void
    {
        $this->assertThrows(
            fn () => Leg::factory()->train()->create(['departure_at' => '2026-08-11 10:00', 'arrival_at' => '2026-08-11 09:59']),
            InvalidLegException::class,
            'TimeRunsForward',
        );

        $this->assertModelExists(Leg::factory()->train()->create(['departure_at' => '2026-08-11 10:00', 'arrival_at' => '2026-08-11 10:00']));
    }

    public function test_6_a_stay_departs_after_it_arrives(): void
    {
        $this->assertThrows(
            fn () => Stay::factory()->create(['arrival' => '2026-08-14', 'departure' => '2026-08-11']),
            InvalidStayException::class,
            'DatesRunForward',
        );

        $this->assertModelExists(Stay::factory()->create(['arrival' => '2026-08-11', 'departure' => '2026-08-14']));
        $this->assertModelExists(Stay::factory()->create(['arrival' => '2026-08-14', 'departure' => '2026-08-14']));
    }

    public function test_7_with_both_times_set_there_is_no_stored_duration(): void
    {
        $this->assertThrows(
            fn () => Leg::factory()->train()->create(['duration_minutes' => 54]),
            InvalidLegException::class,
            'TimesSetTheDuration',
        );

        $this->assertModelExists(Leg::factory()->train()->create());
        $this->assertModelExists(Leg::factory()->transfer()->create(['duration_minutes' => 11]));
    }

    public function test_the_guard_also_stops_an_update(): void
    {
        $transfer = Leg::factory()->transfer()->create();
        $station = $transfer->to_station_id;

        $this->assertThrows(
            fn () => $transfer->update(['to_station_id' => Station::factory()->create()->id]),
            InvalidLegException::class,
            'TransferStaysInOnePlace',
        );

        $this->assertSame($station, $transfer->fresh()?->to_station_id);
    }
}
