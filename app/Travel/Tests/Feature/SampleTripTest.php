<?php

namespace App\Travel\Tests\Feature;

use App\Models\Booking;
use App\Travel\Data\CityData;
use App\Travel\Data\JourneyData;
use App\Travel\Data\LegData;
use App\Travel\Data\PharmacyData;
use App\Travel\Data\StayData;
use App\Travel\Data\TransitLineData;
use App\Travel\Data\TravelDocumentData;
use App\Travel\Database\Seeders\SampleTripSeeder;
use App\Travel\Enums\LegType;
use App\Travel\Models\Leg;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * The read model checked against the real trip of the sample seeder.
 */
class SampleTripTest extends TestCase
{
    use RefreshDatabase;

    private Booking $booking;

    private TravelDocumentData $document;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SampleTripSeeder::class);
        $this->booking = Booking::where('internal_notes', SampleTripSeeder::BOOKING_NOTE)->sole();
        $this->document = TravelDocumentData::fromBooking($this->booking, 'De Vézère-vallei & Bordeaux', []);
    }

    public function test_the_booking_has_three_journeys_in_order(): void
    {
        $this->assertSame(
            [['Heenreis', '2026-08-11'], ['Reisdag 2', '2026-08-14'], ['Terugreis', '2026-08-16']],
            array_map(fn (JourneyData $journey) => [$journey->label, $journey->date?->toDateString()], $this->document->journeys),
        );
    }

    public function test_the_outbound_journey_has_eleven_legs_in_order(): void
    {
        $this->assertSame(
            [
                [LegType::Train, 'Nijmegen', 'Utrecht Centraal'],
                [LegType::Transfer, 'Utrecht Centraal', 'Utrecht Centraal'],
                [LegType::Train, 'Utrecht Centraal', 'Rotterdam Centraal'],
                [LegType::Transfer, 'Rotterdam Centraal', 'Rotterdam Centraal'],
                [LegType::Train, 'Rotterdam Centraal', 'Paris Gare du Nord'],
                [LegType::Metro, 'Paris Gare du Nord', 'Paris Gare Montparnasse'],
                [LegType::Train, 'Paris Gare Montparnasse', 'Bordeaux Saint-Jean'],
                [LegType::Transfer, 'Bordeaux Saint-Jean', 'Bordeaux Saint-Jean'],
                [LegType::Train, 'Bordeaux Saint-Jean', 'Perigueux'],
                [LegType::Transfer, 'Perigueux', 'Perigueux'],
                [LegType::Train, 'Perigueux', 'Les Eyzies'],
            ],
            array_map(fn (LegData $leg) => [$leg->type, $leg->fromStation, $leg->toStation], $this->document->journeys[0]->legs),
        );
    }

    public function test_the_durations_come_from_the_times_or_else_the_stored_duration(): void
    {
        $this->assertSame(
            [54, 11, 37, 33, 187, 102, 136, 24, 85, 47, 36],
            array_map(fn (LegData $leg) => $leg->durationMinutes, $this->document->journeys[0]->legs),
        );
    }

    public function test_the_stays_derive_their_nights(): void
    {
        $this->assertSame(
            [['Hotel Le Cro-Magnon', 3], ["Guest house L'Esprit des Chartrons", 2]],
            array_map(fn (StayData $stay) => [$stay->accommodation->name, $stay->nights], $this->document->stays),
        );
    }

    public function test_the_bordeaux_accommodation_has_a_three_leg_arrival_route(): void
    {
        $guestHouse = $this->document->stays[1]->accommodation;

        $this->assertSame('Bordeaux', $guestHouse->city);
        $this->assertSame(
            [LegType::Tram, LegType::Tram, LegType::Walk],
            array_map(fn (LegData $leg) => $leg->type, $guestHouse->arrivalRoute),
        );
    }

    public function test_the_first_tram_leg_has_two_lines_each_with_its_own_direction(): void
    {
        $tram = $this->document->stays[1]->accommodation->arrivalRoute[0];

        $this->assertSame(
            [['Tram C', 'Parc des Expositions'], ['Tram D', 'Hippodrome']],
            array_map(fn (TransitLineData $line) => [$line->name, $line->direction], $tram->transitLines),
        );
    }

    public function test_the_cities_of_the_trip_bring_both_pharmacies(): void
    {
        $this->assertSame(
            [
                'Nijmegen' => [],
                'Utrecht' => [],
                'Rotterdam' => [],
                'Paris' => [],
                'Bordeaux' => ['Pharmacie de la Place Picard'],
                'Perigueux' => [],
                'Les Eyzies' => ['Pharmacie de la Préhistoire'],
            ],
            collect($this->document->cities)->mapWithKeys(fn (CityData $city) => [
                $city->name => array_map(fn (PharmacyData $pharmacy) => $pharmacy->name, $city->pharmacies),
            ])->all(),
        );
    }

    public function test_deleting_a_journey_deletes_its_legs_and_their_transit_lines(): void
    {
        $outbound = $this->booking->journeys()->firstOrFail();
        $legIds = $outbound->legs()->pluck('id');

        $outbound->delete();

        $this->assertSame(0, Leg::whereKey($legIds)->count());
        $this->assertSame(0, DB::table('travel_leg_transit_line')->whereIn('leg_id', $legIds)->count());
        $this->assertSame(9, Leg::count(), 'The other journeys and the arrival route keep their legs.');
    }
}
