<?php

namespace App\Travel\Tests\Feature;

use App\Models\Booking;
use App\Models\User;
use App\Travel\Contracts\BookingDetails;
use App\Travel\Database\Seeders\SampleTripSeeder;
use App\Travel\Documents\TravelDocuments;
use App\Travel\Models\City;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TravelDocumentsTest extends TestCase
{
    use RefreshDatabase;

    private Booking $booking;

    protected function setUp(): void
    {
        parent::setUp();

        // The module reads the title and the travelers through the contract the app binds; the tests bind their own.
        $this->app->instance(BookingDetails::class, new class implements BookingDetails
        {
            public function title(Booking $booking): string
            {
                return 'De Vézère-vallei & Bordeaux';
            }

            public function travelerNames(Booking $booking): array
            {
                return ['Anna de Vries', 'Bram de Vries'];
            }

            public function emergencyPhone(Booking $booking): string
            {
                return '+31 6 12 34 56 78';
            }
        });

        $this->seed(SampleTripSeeder::class);
        $this->booking = Booking::where('internal_notes', SampleTripSeeder::BOOKING_NOTE)->sole();
    }

    public function test_each_part_has_the_title_the_travelers_its_own_tab_and_the_emergency_numbers(): void
    {
        $html = $this->render();

        foreach (['reisschema', 'verblijf', 'praktisch'] as $part) {
            $this->assertSeeInOrder(substr($html, (int) strpos($html, "id=\"{$part}\"")), [
                '<h1 class="w8">De Vézère-vallei &amp; Bordeaux</h1>',
                'Voor Anna de Vries en Bram de Vries',
                "href=\"#{$part}\" class=\"tab tab-active w7\"",
                'Noodnummer Frankrijk',
                '112 of 15',
                'href="tel:+31612345678">+31 6 12 34 56 78</a>',
            ]);
        }
    }

    public function test_the_schedule_puts_the_nights_between_the_journeys(): void
    {
        $this->assertSeeInOrder($this->render(), [
            'Heenreis',
            'Verblijf · 3 nachten',
            'Hotel Le Cro-Magnon',
            'Reisdag 2',
            'Verblijf · 2 nachten',
            'Guest house L&#039;Esprit des Chartrons',
            'Terugreis',
            'id="verblijf"',
        ]);
    }

    public function test_the_schedule_shows_each_leg_with_its_times_lines_and_ticket(): void
    {
        $this->assertSeeInOrder($this->render(), [
            '<div class="day-weekday w8">DI</div>',
            '<div class="day-number w8">11</div>',
            '<div class="day-month w6">aug</div>',
            'Nijmegen → Les Eyzies',
            '05:43 – 18:15 · 5 overstappen',
            'NS Intercity',
            'richting Den Helder',
            '54 min',
            '<td class="time w8">05:43</td>',
            'Nijmegen',
            '<td class="time w8">06:37</td>',
            'Utrecht Centraal',
            'Overstappen in <strong class="ink w7">Utrecht Centraal</strong>',
            '11 min · kort',
            'Eurostar',
            'trein 9416',
            '3u07',
            'Rijtuig 7',
            'Stoel 34',
            'href="https://www.eurostar.com/nl-nl/reisinformatie/dienstregeling/9416?date=2026-08-11"',
        ]);
    }

    public function test_the_accommodations_show_their_details_and_the_way_from_the_station(): void
    {
        $this->assertSeeInOrder($this->render(), [
            'id="verblijf"',
            'Verblijf 2 · 14 – 16 aug · 2 nachten',
            'Bordeaux',
            'Guest house L&#039;Esprit des Chartrons',
            '17 bis Rue Borie, 33300 Bordeaux',
            'vanaf 16:00',
            'tot 11:30',
            'Vanaf Bordeaux Saint-Jean',
            'aria-label="Tram C">C</div>',
            'Parc des Expositions',
            'Steek de trambaan over',
        ]);
    }

    public function test_the_practical_information_lists_the_pharmacies_of_the_places_the_travelers_stay(): void
    {
        City::where('name', 'Paris')->sole()->pharmacies()->create(['name' => 'Pharmacie de la Gare Montparnasse']);

        $html = $this->render();
        $practical = substr($html, (int) strpos($html, 'id="praktisch"'));

        $this->assertSeeInOrder($practical, ['Apotheken in de buurt', 'Les Eyzies', 'Pharmacie de la Préhistoire', 'Bordeaux', 'Pharmacie de la Place Picard']);
        $this->assertStringNotContainsString('Pharmacie de la Gare Montparnasse', $practical);
        $this->assertStringNotContainsString('Nijmegen', $practical);
    }

    public function test_an_admin_opens_the_pdf(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        $response = $this->get(route('admin.bookings.travel-documents', $this->booking))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');

        $this->assertStringStartsWith('%PDF', (string) $response->getContent());
        $this->assertStringContainsString("inline; filename=reisdocumenten-{$this->booking->reference}.pdf", (string) $response->headers->get('Content-Disposition'));
    }

    public function test_only_an_admin_opens_the_pdf(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'guest']));

        $this->get(route('admin.bookings.travel-documents', $this->booking))->assertForbidden();
    }

    private function render(): string
    {
        $documents = app(TravelDocuments::class);

        return view('travel::documents.pdf', ['document' => $documents->for($this->booking), 'fonts' => TravelDocuments::fontPath()])->render();
    }

    /**
     * @param  list<string>  $texts
     */
    private function assertSeeInOrder(string $html, array $texts): void
    {
        $offset = 0;

        foreach ($texts as $text) {
            $position = strpos($html, $text, $offset);
            $this->assertNotFalse($position, "[{$text}] is missing, or comes too early.");
            $offset = $position + strlen($text);
        }
    }
}
