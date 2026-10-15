<?php

namespace App\Travel\Tests\Unit;

use App\Travel\Data\InfoLink;
use PHPUnit\Framework\TestCase;

class InfoLinkTest extends TestCase
{
    private const EUROSTAR = 'https://www.eurostar.com/nl-nl/reisinformatie/dienstregeling/{%leg_number%}?date={%journey_date%}';

    public function test_it_fills_in_the_variables_of_the_leg(): void
    {
        $this->assertSame(
            'https://www.eurostar.com/nl-nl/reisinformatie/dienstregeling/9416?date=2026-08-11',
            InfoLink::fill(self::EUROSTAR, ['leg_number' => '9416', 'journey_date' => '2026-08-11']),
        );
    }

    public function test_it_allows_spaces_inside_the_braces_and_encodes_the_values(): void
    {
        $this->assertSame(
            'https://www.ns.nl/reisplanner?trein=IC%203555',
            InfoLink::fill('https://www.ns.nl/reisplanner?trein={% leg_number %}', ['leg_number' => 'IC 3555']),
        );
    }

    public function test_a_link_without_variables_stays_as_it_is(): void
    {
        $this->assertSame('https://www.infotbm.com', InfoLink::fill('https://www.infotbm.com', []));
    }

    public function test_a_link_with_a_variable_without_a_value_is_no_link(): void
    {
        $this->assertNull(InfoLink::fill(self::EUROSTAR, ['leg_number' => '9416', 'journey_date' => null]));
        $this->assertNull(InfoLink::fill(self::EUROSTAR, ['leg_number' => '', 'journey_date' => '2026-08-11']));
        $this->assertNull(InfoLink::fill('https://example.com/{%leg_nummer%}', ['leg_number' => '9416']));
    }

    public function test_it_tells_a_link_with_variables(): void
    {
        $this->assertTrue(InfoLink::hasVariables(self::EUROSTAR));
        $this->assertTrue(InfoLink::hasVariables('https://example.com/{% leg_nummer %}'));
        $this->assertFalse(InfoLink::hasVariables('https://www.infotbm.com/?q=%7Bleg%7D'));
    }

    public function test_it_names_the_variables_it_does_not_know(): void
    {
        $this->assertSame([], InfoLink::unknownVariables(self::EUROSTAR));
        $this->assertSame(
            ['leg_nummer', 'date'],
            InfoLink::unknownVariables('https://example.com/{%leg_nummer%}/{% date %}/{%leg_nummer%}'),
        );
    }
}
