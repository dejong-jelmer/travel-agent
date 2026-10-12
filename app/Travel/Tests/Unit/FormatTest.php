<?php

namespace App\Travel\Tests\Unit;

use App\Travel\Documents\Format;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;

class FormatTest extends TestCase
{
    public function test_it_writes_dates_in_dutch(): void
    {
        $date = CarbonImmutable::parse('2026-08-11');

        $this->assertSame('dinsdag 11 augustus', Format::date($date));
        $this->assertSame('di 11 aug', Format::shortDate($date));
        $this->assertSame('', Format::date(null));
    }

    public function test_it_writes_times_as_they_are(): void
    {
        $this->assertSame('05:43', Format::time(CarbonImmutable::parse('2026-08-11 05:43')));
        $this->assertSame('16:00', Format::timeOfDay('16:00:00'));
        $this->assertSame('', Format::time(null));
    }

    public function test_it_writes_durations_and_nights(): void
    {
        $this->assertSame('54 min', Format::duration(54));
        $this->assertSame('2 u', Format::duration(120));
        $this->assertSame('2 u 16 min', Format::duration(136));
        $this->assertSame('', Format::duration(null));
        $this->assertSame('1 nacht', Format::nights(1));
        $this->assertSame('3 nachten', Format::nights(3));
    }

    public function test_it_writes_the_stations_of_a_leg(): void
    {
        $this->assertSame('Nijmegen → Utrecht Centraal', Format::stations('Nijmegen', 'Utrecht Centraal'));
        $this->assertSame('Perigueux', Format::stations('Perigueux', 'Perigueux'));
        $this->assertSame('Bordeaux Saint-Jean', Format::stations('Bordeaux Saint-Jean', null));
        $this->assertSame('→ Chartrons', Format::stations(null, 'Chartrons'));
        $this->assertSame('', Format::stations(null, null));
    }

    public function test_it_leaves_out_what_a_ticket_or_an_accommodation_does_not_have(): void
    {
        $this->assertSame('Nr. 8413 · Rijtuig 8 - onder · Stoel 824', Format::ticket('8413', '8 - onder', '824'));
        $this->assertSame('Nr. 8413', Format::ticket('8413', null, null));
        $this->assertSame('Inchecken vanaf 16:00, uitchecken voor 11:30', Format::checkTimes('16:00:00', '11:30:00'));
        $this->assertSame('Uitchecken voor 11:30', Format::checkTimes(null, '11:30:00'));
        $this->assertSame('', Format::checkTimes(null, null));
    }
}
