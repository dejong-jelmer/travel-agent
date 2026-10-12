<?php

namespace App\Travel\Tests\Feature;

use App\Models\Booking;
use App\Travel\Data\CityData;
use App\Travel\Data\JourneyData;
use App\Travel\Data\LegData;
use App\Travel\Data\PharmacyData;
use App\Travel\Data\TransitLineData;
use App\Travel\Data\TravelDocumentData;
use App\Travel\Enums\AccommodationType;
use App\Travel\Enums\LegType;
use App\Travel\Enums\TransitMode;
use App\Travel\Models\City;
use App\Travel\Models\TransitLine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TravelDocumentDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_passes_the_title_and_traveler_names_from_the_caller(): void
    {
        $document = TravelDocumentData::fromBooking($this->booking(), 'De Vézère-vallei & Bordeaux', ['Anna de Vries', 'Bram de Vries']);

        $this->assertSame('De Vézère-vallei & Bordeaux', $document->title);
        $this->assertSame(['Anna de Vries', 'Bram de Vries'], $document->travelerNames);
    }

    public function test_it_gathers_the_journeys_with_their_legs_in_order(): void
    {
        $document = TravelDocumentData::fromBooking($this->booking(), 'Titel', []);

        $this->assertSame(['Heenreis', 'Terugreis'], array_map(fn (JourneyData $journey) => $journey->label, $document->journeys));

        $outbound = $document->journeys[0];
        $this->assertSame('2026-08-11', $outbound->date?->toDateString());
        $this->assertSame([LegType::Train, LegType::Transfer], array_map(fn (LegData $leg) => $leg->type, $outbound->legs));

        $tgv = $outbound->legs[0];
        $this->assertSame('Paris Gare Montparnasse', $tgv->fromStation);
        $this->assertSame('Bordeaux Saint-Jean', $tgv->toStation);
        $this->assertSame('2026-08-11 12:47', $tgv->departureAt?->format('Y-m-d H:i'));
        $this->assertSame('8413', $tgv->number);
        $this->assertSame('8 - onder', $tgv->carriage);
        $this->assertSame('TGV INOUI', $tgv->transitLines[0]->name);
        $this->assertSame('Bordeaux Saint-Jean', $tgv->transitLines[0]->direction);
    }

    public function test_it_gathers_the_stays_with_their_accommodation_and_arrival_route(): void
    {
        $document = TravelDocumentData::fromBooking($this->booking(), 'Titel', []);

        [$hotel, $guestHouse] = $document->stays;
        $this->assertSame('Hotel Le Cro-Magnon', $hotel->accommodation->name);
        $this->assertSame(3, $hotel->nights);
        $this->assertSame([], $hotel->accommodation->arrivalRoute);

        $this->assertSame(AccommodationType::GuestHouse, $guestHouse->accommodation->type);
        $this->assertSame('Bordeaux', $guestHouse->accommodation->city);
        $this->assertSame(2, $guestHouse->nights);
        $this->assertSame([LegType::Tram, LegType::Walk], array_map(fn (LegData $leg) => $leg->type, $guestHouse->accommodation->arrivalRoute));
        $this->assertSame(
            [['Tram C', 'Parc des Expositions'], ['Tram D', 'Hippodrome']],
            array_map(fn (TransitLineData $line) => [$line->name, $line->direction], $guestHouse->accommodation->arrivalRoute[0]->transitLines),
        );
    }

    public function test_it_derives_the_cities_of_the_journey_stations_and_the_stays_with_their_pharmacies(): void
    {
        $document = TravelDocumentData::fromBooking($this->booking(), 'Titel', []);

        $this->assertSame(
            [
                'Paris' => [],
                'Bordeaux' => ['Pharmacie de la Place Picard'],
                'Les Eyzies' => ['Pharmacie de la Préhistoire'],
            ],
            collect($document->cities)->mapWithKeys(fn (CityData $city) => [
                $city->name => array_map(fn (PharmacyData $pharmacy) => $pharmacy->name, $city->pharmacies),
            ])->all(),
        );
    }

    public function test_it_loads_the_booking_without_lazy_loading(): void
    {
        $booking = $this->booking()->fresh();

        $this->assertNoLazyLoading(fn () => TravelDocumentData::fromBooking($booking, 'Titel', []));
    }

    /**
     * A cut-down version of "De Vézère-vallei & Bordeaux". Les Eyzies is only reached through its stay, and journeys,
     * legs and transit lines are created out of order, so only their position decides.
     */
    private function booking(): Booking
    {
        $paris = City::create(['name' => 'Paris', 'country' => 'FR']);
        $bordeaux = City::create(['name' => 'Bordeaux', 'country' => 'FR']);
        $lesEyzies = City::create(['name' => 'Les Eyzies', 'country' => 'FR']);
        $bordeaux->pharmacies()->create(['name' => 'Pharmacie de la Place Picard', 'address' => '1 Cr Saint-Louis']);
        $lesEyzies->pharmacies()->create(['name' => 'Pharmacie de la Préhistoire', 'address' => '1 Av. de la Forge']);
        City::create(['name' => 'Lyon', 'country' => 'FR'])->pharmacies()->create(['name' => 'Pharmacie Bellecour']);

        $montparnasse = $paris->stations()->create(['name' => 'Paris Gare Montparnasse']);
        $saintJean = $bordeaux->stations()->create(['name' => 'Bordeaux Saint-Jean']);
        $tgv = TransitLine::create(['mode' => TransitMode::Train, 'name' => 'TGV INOUI']);

        $booking = Booking::factory()->create();

        $booking->journeys()->create(['label' => 'Terugreis', 'date' => '2026-08-16', 'position' => 1])
            ->legs()->create([
                'type' => LegType::Train,
                'from_station_id' => $saintJean->id,
                'to_station_id' => $montparnasse->id,
            ])
            ->transitLines()->attach($tgv, ['direction' => 'Paris Gare Montparnasse']);

        $outbound = $booking->journeys()->create(['label' => 'Heenreis', 'date' => '2026-08-11', 'position' => 0]);
        $outbound->legs()->create([
            'type' => LegType::Transfer,
            'from_station_id' => $saintJean->id,
            'to_station_id' => $saintJean->id,
            'duration_minutes' => 24,
            'position' => 1,
        ]);
        $outbound->legs()->create([
            'type' => LegType::Train,
            'from_station_id' => $montparnasse->id,
            'to_station_id' => $saintJean->id,
            'departure_at' => '2026-08-11 12:47',
            'arrival_at' => '2026-08-11 15:03',
            'number' => '8413',
            'carriage' => '8 - onder',
            'seat' => '824',
        ])->transitLines()->attach($tgv, ['direction' => 'Bordeaux Saint-Jean']);

        $hotel = $lesEyzies->accommodations()->create(['name' => 'Hotel Le Cro-Magnon', 'type' => AccommodationType::Hotel]);
        $guestHouse = $bordeaux->accommodations()->create([
            'name' => "Guest house L'Esprit des Chartrons",
            'type' => AccommodationType::GuestHouse,
        ]);
        $guestHouse->legs()->create(['type' => LegType::Walk, 'note' => 'laatste 100m lopen', 'position' => 1]);
        $guestHouse->legs()->create(['type' => LegType::Tram, 'from_station_id' => $saintJean->id])
            ->transitLines()->attach([
                TransitLine::create(['mode' => TransitMode::Tram, 'name' => 'Tram D'])->id => ['direction' => 'Hippodrome', 'position' => 1],
                TransitLine::create(['mode' => TransitMode::Tram, 'name' => 'Tram C'])->id => ['direction' => 'Parc des Expositions', 'position' => 0],
            ]);

        $booking->stays()->create([
            'accommodation_id' => $guestHouse->id,
            'arrival' => '2026-08-14',
            'departure' => '2026-08-16',
            'position' => 1,
        ]);
        $booking->stays()->create(['accommodation_id' => $hotel->id, 'arrival' => '2026-08-11', 'departure' => '2026-08-14']);

        return $booking;
    }
}
