<?php

namespace App\Travel\Tests\Unit;

use App\Travel\Data\AccommodationData;
use App\Travel\Data\JourneyData;
use App\Travel\Data\StayData;
use App\Travel\Data\TravelDocumentData;
use App\Travel\Enums\AccommodationType;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;

/**
 * The schedule merges the journeys and stays by date, each list keeping its own order.
 */
class ScheduleTest extends TestCase
{
    public function test_the_stays_fall_between_the_journeys_by_date(): void
    {
        $document = $this->document(
            journeys: [$this->journey('Heenreis', '2026-08-11'), $this->journey('Reisdag 2', '2026-08-14'), $this->journey('Terugreis', '2026-08-16')],
            stays: [$this->stay('Hotel Le Cro-Magnon', '2026-08-11'), $this->stay('Guest house', '2026-08-14')],
        );

        $this->assertSame(
            ['Heenreis', 'Hotel Le Cro-Magnon', 'Reisdag 2', 'Guest house', 'Terugreis'],
            $this->names($document->schedule()),
        );
    }

    public function test_each_list_keeps_its_own_order_when_the_dates_disagree(): void
    {
        $document = $this->document(
            journeys: [$this->journey('Terugreis', '2026-08-16'), $this->journey('Heenreis', '2026-08-11')],
            stays: [$this->stay('Hotel', '2026-08-12')],
        );

        $this->assertSame(['Hotel', 'Terugreis', 'Heenreis'], $this->names($document->schedule()));
    }

    public function test_what_has_no_date_waits_for_the_other_list(): void
    {
        $document = $this->document(
            journeys: [$this->journey('Heenreis', null), $this->journey('Terugreis', '2026-08-16')],
            stays: [$this->stay('Hotel', '2026-08-11'), $this->stay('Pension', null)],
        );

        $this->assertSame(['Hotel', 'Heenreis', 'Terugreis', 'Pension'], $this->names($document->schedule()));
    }

    /**
     * @param  list<JourneyData>  $journeys
     * @param  list<StayData>  $stays
     */
    private function document(array $journeys, array $stays): TravelDocumentData
    {
        return new TravelDocumentData('Titel', [], $journeys, $stays, []);
    }

    private function journey(string $label, ?string $date): JourneyData
    {
        return new JourneyData($label, $date === null ? null : CarbonImmutable::parse($date), []);
    }

    private function stay(string $name, ?string $arrival): StayData
    {
        return new StayData(
            arrival: $arrival === null ? null : CarbonImmutable::parse($arrival),
            departure: null,
            nights: null,
            accommodation: new AccommodationData($name, AccommodationType::Hotel, 'Bordeaux', null, null, null, null, null, null, null, null, []),
        );
    }

    /**
     * @param  list<JourneyData|StayData>  $schedule
     * @return list<string>
     */
    private function names(array $schedule): array
    {
        return array_map(fn (JourneyData|StayData $entry) => $entry instanceof JourneyData ? $entry->label : $entry->accommodation->name, $schedule);
    }
}
