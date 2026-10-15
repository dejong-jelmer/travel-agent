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
        $this->assertSame(['weekday' => 'DI', 'day' => '11', 'month' => 'aug'], Format::day($date));
        $this->assertSame('', Format::date(null));
    }

    public function test_it_writes_a_period_with_as_little_repetition_as_it_can(): void
    {
        $date = fn (string $date) => CarbonImmutable::parse($date);

        $this->assertSame('11 – 16 augustus 2026', Format::period($date('2026-08-11'), $date('2026-08-16')));
        $this->assertSame('30 juli – 2 augustus 2026', Format::period($date('2026-07-30'), $date('2026-08-02')));
        $this->assertSame('30 december 2026 – 2 januari 2027', Format::period($date('2026-12-30'), $date('2027-01-02')));
        $this->assertSame('11 augustus 2026', Format::period($date('2026-08-11'), $date('2026-08-11')));
        $this->assertSame('11 – 14 aug', Format::shortPeriod($date('2026-08-11'), $date('2026-08-14')));
        $this->assertSame('30 jul – 2 aug', Format::shortPeriod($date('2026-07-30'), $date('2026-08-02')));
        $this->assertSame('', Format::period(null, $date('2026-08-16')));
    }

    public function test_it_writes_times_as_they_are(): void
    {
        $this->assertSame('05:43', Format::time(CarbonImmutable::parse('2026-08-11 05:43')));
        $this->assertSame('16:00', Format::timeOfDay('16:00:00'));
        $this->assertSame('', Format::time(null));
    }

    public function test_it_writes_durations_nights_and_changes_as_the_design(): void
    {
        $this->assertSame('54 min', Format::duration(54));
        $this->assertSame('1u00', Format::duration(60));
        $this->assertSame('3u07', Format::duration(187));
        $this->assertSame('', Format::duration(null));
        $this->assertSame('1 nacht', Format::nights(1));
        $this->assertSame('3 nachten', Format::nights(3));
        $this->assertSame('direct', Format::changes(0));
        $this->assertSame('1 overstap', Format::changes(1));
        $this->assertSame('5 overstappen', Format::changes(5));
    }

    public function test_it_lists_names_in_dutch(): void
    {
        $this->assertSame('Frankrijk', Format::list(['Frankrijk']));
        $this->assertSame('Frankrijk en Spanje', Format::list(['Frankrijk', 'Spanje']));
        $this->assertSame('Frankrijk, Spanje en Portugal', Format::list(['Frankrijk', 'Spanje', 'Portugal']));
    }

    public function test_it_turns_contact_details_into_links(): void
    {
        $this->assertSame(['54 Avenue de la Préhistoire', '24620 Les Eyzies-de-Tayac-Sireuil'], Format::addressLines('54 Avenue de la Préhistoire, 24620 Les Eyzies-de-Tayac-Sireuil'));
        $this->assertSame(['Rue Borie'], Format::addressLines('Rue Borie'));
        $this->assertSame('https://maps.google.com/?q=17+bis+Rue+Borie%2C+33300+Bordeaux', Format::mapUrl('17 bis Rue Borie, 33300 Bordeaux'));
        $this->assertSame('tel:+33553069706', Format::phoneUrl('+33 (0)5 53 06 97 06'));
        $this->assertSame('tel:+31612345678', Format::phoneUrl('+31 6 12 34 56 78'));
    }

    public function test_it_splits_walking_directions_into_steps(): void
    {
        $this->assertSame(
            ['Sla bij het verlaten van het station rechtsaf.', 'Loop 200m tot het einde van de straat.', 'Je vindt het hotel aan je linkerkant.'],
            Format::steps('Sla bij het verlaten van het station rechtsaf. Loop 200m tot het einde van de straat. Je vindt het hotel aan je linkerkant.'),
        );
        $this->assertSame(['Steek over', 'Neem de eerste links'], Format::steps("Steek over\nNeem de eerste links"));
        $this->assertSame([], Format::steps(null));
        $this->assertSame([], Format::steps('  '));
    }

    public function test_it_names_a_line_on_its_badge(): void
    {
        $this->assertSame('4', Format::lineBadge('Lijn 4'));
        $this->assertSame('C', Format::lineBadge('Tram C'));
        $this->assertSame('L31', Format::lineBadge('TER NA L31'));
        $this->assertSame('E', Format::lineBadge('Eurostar'));
    }
}
