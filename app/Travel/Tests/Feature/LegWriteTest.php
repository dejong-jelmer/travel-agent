<?php

namespace App\Travel\Tests\Feature;

use App\Travel\Actions\CreateLeg;
use App\Travel\Actions\UpdateLeg;
use App\Travel\Enums\LegType;
use App\Travel\Enums\TransitMode;
use App\Travel\Exceptions\InvalidLegException;
use App\Travel\Models\Accommodation;
use App\Travel\Models\Journey;
use App\Travel\Models\TransitLine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LegWriteTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_leg_is_created_with_its_lines_in_order_each_with_its_own_direction(): void
    {
        $tramC = TransitLine::factory()->create(['mode' => TransitMode::Tram, 'name' => 'Tram C']);
        $tramD = TransitLine::factory()->create(['mode' => TransitMode::Tram, 'name' => 'Tram D']);

        $leg = app(CreateLeg::class)->handle(
            Accommodation::factory()->create(),
            ['type' => LegType::Tram, 'duration_minutes' => 10, 'note' => 'uitstappen: Quinconces en overstappen'],
            [[$tramD->id, 'Hippodrome'], [$tramC->id, 'Parc des Expositions']],
        );

        $this->assertSame(
            [['Tram D', 'Hippodrome'], ['Tram C', 'Parc des Expositions']],
            $leg->transitLines->map(fn (TransitLine $line) => [$line->name, $line->pivot->direction])->all(),
        );
    }

    public function test_creating_a_leg_rolls_back_when_its_lines_are_refused(): void
    {
        $line = TransitLine::factory()->create();

        $this->assertThrows(
            fn () => app(CreateLeg::class)->handle(Journey::factory()->create(), ['type' => LegType::Walk], [[$line->id, null]]),
            InvalidLegException::class,
            'OnlyTransitCarriesLines',
        );

        $this->assertDatabaseCount('travel_legs', 0);
        $this->assertDatabaseCount('travel_leg_transit_line', 0);
    }

    public function test_a_metro_line_is_refused_on_a_tram_leg(): void
    {
        $metro = TransitLine::factory()->create(['mode' => TransitMode::Metro, 'name' => 'Lijn 4']);

        $this->assertThrows(
            fn () => app(CreateLeg::class)->handle(Journey::factory()->create(), ['type' => LegType::Tram], [[$metro->id, 'Bagneux']]),
            InvalidLegException::class,
            'LinesMatchTheLeg',
        );

        $this->assertDatabaseCount('travel_legs', 0);
    }

    public function test_a_leg_turned_into_a_walk_must_drop_its_lines(): void
    {
        $tramLine = TransitLine::factory()->create(['mode' => TransitMode::Tram]);
        $leg = app(CreateLeg::class)->handle(Journey::factory()->create(), ['type' => LegType::Tram], [[$tramLine->id, 'Hippodrome']]);

        $this->assertThrows(
            fn () => app(UpdateLeg::class)->handle($leg, ['type' => LegType::Walk], [[$tramLine->id, 'Hippodrome']]),
            InvalidLegException::class,
            'OnlyTransitCarriesLines',
        );
        $this->assertSame(LegType::Tram, $leg->fresh()?->type);

        app(UpdateLeg::class)->handle($leg->refresh(), ['type' => LegType::Walk], []);

        $this->assertSame(LegType::Walk, $leg->fresh()?->type);
        $this->assertDatabaseCount('travel_leg_transit_line', 0);
    }
}
