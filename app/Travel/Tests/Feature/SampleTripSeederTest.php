<?php

namespace App\Travel\Tests\Feature;

use App\Travel\Database\Seeders\SampleTripSeeder;
use App\Travel\Models\Leg;
use App\Travel\Models\Stay;
use App\Travel\Validation\LegInvariants;
use App\Travel\Validation\StayInvariants;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SampleTripSeederTest extends TestCase
{
    use RefreshDatabase;

    private const EXPECTED_ROWS = [
        'travel_cities' => 7,
        'travel_stations' => 8,
        'travel_transit_lines' => 9,
        'travel_pharmacies' => 2,
        'travel_accommodations' => 2,
        'bookings' => 1,
        'travel_journeys' => 3,
        'travel_stays' => 2,
        'travel_legs' => 20,
        'travel_leg_transit_line' => 15,
    ];

    public function test_it_seeds_the_sample_trip_once(): void
    {
        $this->seed(SampleTripSeeder::class);
        $this->assertSame(self::EXPECTED_ROWS, $this->rowCounts());

        $this->seed(SampleTripSeeder::class);
        $this->assertSame(self::EXPECTED_ROWS, $this->rowCounts());
    }

    public function test_the_sample_trip_keeps_every_invariant(): void
    {
        // The models refuse to save a leg or stay that breaks one, but the seeder attaches the lines directly.
        $this->seed(SampleTripSeeder::class);

        foreach (Leg::with('transitLines')->get() as $leg) {
            $this->assertSame(
                [],
                [...LegInvariants::check($leg), ...LegInvariants::checkTransitLines($leg->type, $leg->transitLines)],
                "Leg {$leg->id} breaks an invariant.",
            );
        }

        foreach (Stay::all() as $stay) {
            $this->assertSame([], StayInvariants::check($stay), "Stay {$stay->id} breaks an invariant.");
        }
    }

    /**
     * @return array<string, int>
     */
    private function rowCounts(): array
    {
        $counts = [];
        foreach (array_keys(self::EXPECTED_ROWS) as $table) {
            $counts[$table] = DB::table($table)->count();
        }

        return $counts;
    }
}
