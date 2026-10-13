<?php

namespace App\Travel\Database\Seeders;

use App\Models\Booking;
use App\Travel\Enums\AccommodationType;
use App\Travel\Enums\LegType;
use App\Travel\Enums\TransitMode;
use App\Travel\Models\Accommodation;
use App\Travel\Models\City;
use App\Travel\Models\Journey;
use App\Travel\Models\Pharmacy;
use App\Travel\Models\Station;
use App\Travel\Models\TransitLine;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * The real trip "De Vézère-vallei & Bordeaux", as a smoke test for the travel models. Safe to run again: library
 * records and the arrival route are only created when missing, and the booking only once.
 *
 *     php artisan db:seed --class="App\Travel\Database\Seeders\SampleTripSeeder"
 */
class SampleTripSeeder extends Seeder
{
    /**
     * Marks the sample booking, so a next run finds it.
     */
    public const BOOKING_NOTE = 'Voorbeeldboeking "De Vézère-vallei & Bordeaux" (SampleTripSeeder)';

    /** @var array<string, Station> */
    private array $stations = [];

    /** @var array<string, TransitLine> */
    private array $transitLines = [];

    public function run(): void
    {
        DB::transaction(function () {
            $this->seedStations();
            $this->seedTransitLines();
            $this->seedPharmacies();
            [$hotel, $guestHouse] = $this->seedAccommodations();
            $this->seedBooking($hotel, $guestHouse);
        });
    }

    private function seedStations(): void
    {
        foreach ([
            'Nijmegen' => ['Nijmegen', 'NL'],
            'Utrecht Centraal' => ['Utrecht', 'NL'],
            'Rotterdam Centraal' => ['Rotterdam', 'NL'],
            'Paris Gare du Nord' => ['Paris', 'FR'],
            'Paris Gare Montparnasse' => ['Paris', 'FR'],
            'Bordeaux Saint-Jean' => ['Bordeaux', 'FR'],
            'Perigueux' => ['Perigueux', 'FR'],
            'Les Eyzies' => ['Les Eyzies', 'FR'],
        ] as $name => [$city, $country]) {
            $this->stations[$name] = Station::firstOrCreate(['city_id' => $this->city($city, $country)->id, 'name' => $name]);
        }
    }

    private function seedTransitLines(): void
    {
        foreach ([
            ['Eurostar', TransitMode::Train, null, 'https://www.eurostar.com/nl-nl/reisinformatie/dienstregeling/{%leg_number%}?date={%journey_date%}'],
            ['TGV INOUI', TransitMode::Train, null, null],
            ['TER NA L31', TransitMode::Train, null, null],
            ['TER NA L34', TransitMode::Train, null, null],
            ['NS Intercity', TransitMode::Train, null, null],
            ['Lijn 4', TransitMode::Metro, '#8e2367', 'https://www.nsinternational.com/nl/overstapstations/overstappen-parijs-gare-du-nord-en-gare-montparnasse'],
            ['Tram B', TransitMode::Tram, '#e4003f', null],
            // The colours of Tram C and Tram D are still unknown.
            ['Tram C', TransitMode::Tram, null, null],
            ['Tram D', TransitMode::Tram, null, null],
        ] as [$name, $mode, $color, $infoLink]) {
            $this->transitLines[$name] = TransitLine::firstOrCreate(
                ['mode' => $mode, 'name' => $name],
                ['color' => $color, 'info_link' => $infoLink],
            );
        }
    }

    private function seedPharmacies(): void
    {
        Pharmacy::firstOrCreate(
            ['city_id' => $this->city('Les Eyzies', 'FR')->id, 'name' => 'Pharmacie de la Préhistoire'],
            ['address' => '1 Av. de la Forge'],
        );
        Pharmacy::firstOrCreate(
            ['city_id' => $this->city('Bordeaux', 'FR')->id, 'name' => 'Pharmacie de la Place Picard'],
            ['address' => '1 Cr Saint-Louis'],
        );
    }

    /**
     * @return array{Accommodation, Accommodation}
     */
    private function seedAccommodations(): array
    {
        $hotel = Accommodation::firstOrCreate(
            ['city_id' => $this->city('Les Eyzies', 'FR')->id, 'name' => 'Hotel Le Cro-Magnon'],
            [
                'type' => AccommodationType::Hotel,
                'address' => '54 Avenue de la Préhistoire, 24620 Les Eyzies-de-Tayac-Sireuil',
                'phone' => '+33 (0)5 53 06 97 06',
                'check_in' => '17:00',
                'check_out' => '11:00',
                'walk_directions' => 'Sla bij het verlaten van het station rechtsaf. Loop 200m tot het einde van de straat. '
                    .'Je vindt het hotel aan je linkerkant.',
            ],
        );

        $guestHouse = Accommodation::firstOrCreate(
            ['city_id' => $this->city('Bordeaux', 'FR')->id, 'name' => "Guest house L'Esprit des Chartrons"],
            [
                'type' => AccommodationType::GuestHouse,
                'address' => '17 bis Rue Borie, 33300 Bordeaux',
                'phone' => '+33 (0)5 56 51 65 87',
                'check_in' => '16:00',
                'check_out' => '11:30',
                'walk_directions' => 'Steek de trambaan over en blijf in de rijrichting van de tram lopen. Neem de eerste links. '
                    .'Loop 100m, je vindt het hotel in het groenbegroeide gebouw aan je rechterhand.',
            ],
        );

        // Reused by every booking, so without dates or times. Quinconces and Chartrons are tram stops, not stations,
        // so the notes name them.
        if ($guestHouse->legs()->doesntExist()) {
            $this->leg($guestHouse, LegType::Tram, from: 'Bordeaux Saint-Jean', minutes: 10,
                lines: ['Tram C' => 'Parc des Expositions', 'Tram D' => 'Hippodrome'], note: 'uitstappen: Quinconces en overstappen');
            $this->leg($guestHouse, LegType::Tram, minutes: 4,
                lines: ['Tram B' => 'Berges de la Garonne'], note: 'uitstappen: Chartrons');
            $this->leg($guestHouse, LegType::Walk, note: 'laatste 100m lopen, hotel aan de rechterhand');
        }

        return [$hotel, $guestHouse];
    }

    private function seedBooking(Accommodation $hotel, Accommodation $guestHouse): void
    {
        if (Booking::where('internal_notes', self::BOOKING_NOTE)->exists()) {
            return;
        }

        // The module stays out of trips, so the booking keeps the placeholder trip of the booking factory.
        $booking = Booking::factory()->create([
            'departure_date' => '2026-08-11',
            'return_date' => '2026-08-16',
            'internal_notes' => self::BOOKING_NOTE,
        ]);

        $outbound = $booking->journeys()->create(['label' => 'Heenreis', 'date' => '2026-08-11', 'position' => 0]);
        $this->leg($outbound, LegType::Train, from: 'Nijmegen', to: 'Utrecht Centraal', departs: '05:43', arrives: '06:37',
            lines: ['NS Intercity' => 'Den Helder']);
        $this->leg($outbound, LegType::Transfer, from: 'Utrecht Centraal', to: 'Utrecht Centraal', minutes: 11);
        $this->leg($outbound, LegType::Train, from: 'Utrecht Centraal', to: 'Rotterdam Centraal', departs: '06:48', arrives: '07:25',
            lines: ['NS Intercity' => 'Rotterdam Centraal']);
        $this->leg($outbound, LegType::Transfer, from: 'Rotterdam Centraal', to: 'Rotterdam Centraal', minutes: 33);
        $this->leg($outbound, LegType::Train, from: 'Rotterdam Centraal', to: 'Paris Gare du Nord', departs: '07:58', arrives: '11:05',
            lines: ['Eurostar' => null], number: '9416', carriage: '7', seat: '34');
        $this->leg($outbound, LegType::Metro, from: 'Paris Gare du Nord', to: 'Paris Gare Montparnasse', minutes: 102,
            lines: ['Lijn 4' => 'Bagneux']);
        $this->leg($outbound, LegType::Train, from: 'Paris Gare Montparnasse', to: 'Bordeaux Saint-Jean', departs: '12:47', arrives: '15:03',
            lines: ['TGV INOUI' => null], number: '8413', carriage: '8 - onder', seat: '824');
        $this->leg($outbound, LegType::Transfer, from: 'Bordeaux Saint-Jean', to: 'Bordeaux Saint-Jean', minutes: 24);
        $this->leg($outbound, LegType::Train, from: 'Bordeaux Saint-Jean', to: 'Perigueux', departs: '15:27', arrives: '16:52',
            lines: ['TER NA L31' => null]);
        $this->leg($outbound, LegType::Transfer, from: 'Perigueux', to: 'Perigueux', minutes: 47);
        $this->leg($outbound, LegType::Train, from: 'Perigueux', to: 'Les Eyzies', departs: '17:39', arrives: '18:15',
            lines: ['TER NA L34' => null]);

        $dayTwo = $booking->journeys()->create(['label' => 'Reisdag 2', 'date' => '2026-08-14', 'position' => 1]);
        $this->leg($dayTwo, LegType::Train, from: 'Les Eyzies', to: 'Perigueux', departs: '06:35', arrives: '07:09',
            lines: ['TER NA L34' => null]);
        $this->leg($dayTwo, LegType::Transfer, from: 'Perigueux', to: 'Perigueux', minutes: 22);
        $this->leg($dayTwo, LegType::Train, from: 'Perigueux', to: 'Bordeaux Saint-Jean', departs: '07:31', arrives: '08:58',
            lines: ['TER NA L31' => null]);

        $return = $booking->journeys()->create(['label' => 'Terugreis', 'date' => '2026-08-16', 'position' => 2]);
        $this->leg($return, LegType::Train, from: 'Bordeaux Saint-Jean', to: 'Paris Gare Montparnasse', departs: '12:46', arrives: '14:55',
            lines: ['TGV INOUI' => null], number: '12288', carriage: '5 - boven', seat: '581');
        $this->leg($return, LegType::Metro, from: 'Paris Gare Montparnasse', to: 'Paris Gare du Nord', minutes: 60,
            lines: ['Lijn 4' => 'Porte de Clignancourt']);
        $this->leg($return, LegType::Train, from: 'Paris Gare du Nord', to: 'Rotterdam Centraal', departs: '15:55', arrives: '19:02',
            lines: ['Eurostar' => null], number: '9363', carriage: '18', seat: '83');

        $booking->stays()->create(['accommodation_id' => $hotel->id, 'arrival' => '2026-08-11', 'departure' => '2026-08-14', 'position' => 0]);
        $booking->stays()->create(['accommodation_id' => $guestHouse->id, 'arrival' => '2026-08-14', 'departure' => '2026-08-16', 'position' => 1]);
    }

    private function city(string $name, string $country): City
    {
        return City::firstOrCreate(['name' => $name, 'country' => $country]);
    }

    /**
     * Add the next leg to a journey or an arrival route. Times are local, on the date of the journey.
     *
     * @param  array<string, string|null>  $lines  Transit line name => direction, in the order shown.
     */
    private function leg(
        Journey|Accommodation $owner,
        LegType $type,
        ?string $from = null,
        ?string $to = null,
        ?string $departs = null,
        ?string $arrives = null,
        ?int $minutes = null,
        array $lines = [],
        ?string $number = null,
        ?string $carriage = null,
        ?string $seat = null,
        ?string $note = null,
    ): void {
        $date = $owner instanceof Journey ? $owner->date?->toDateString() : null;

        $leg = $owner->legs()->create([
            'type' => $type,
            'from_station_id' => $from ? $this->stations[$from]->id : null,
            'to_station_id' => $to ? $this->stations[$to]->id : null,
            'departure_at' => $departs ? "{$date} {$departs}" : null,
            'arrival_at' => $arrives ? "{$date} {$arrives}" : null,
            'duration_minutes' => $minutes,
            'number' => $number,
            'carriage' => $carriage,
            'seat' => $seat,
            'note' => $note,
            'position' => $owner->legs()->count(),
        ]);

        foreach (array_keys($lines) as $position => $name) {
            $leg->transitLines()->attach($this->transitLines[$name], ['direction' => $lines[$name], 'position' => $position]);
        }
    }
}
