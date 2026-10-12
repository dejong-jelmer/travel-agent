<?php

namespace App\Travel\Tests\Feature;

use App\Models\Booking;
use App\Models\User;
use App\Travel\Contracts\BookingDetails;
use App\Travel\Database\Seeders\SampleTripSeeder;
use App\Travel\Documents\TravelDocuments;
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
        });

        $this->seed(SampleTripSeeder::class);
        $this->booking = Booking::where('internal_notes', SampleTripSeeder::BOOKING_NOTE)->sole();
    }

    public function test_the_document_shows_the_title_the_travelers_and_the_three_parts(): void
    {
        $html = $this->render();

        foreach (['De Vézère-vallei &amp; Bordeaux', 'Anna de Vries, Bram de Vries', 'Reisschema', 'Accommodaties', 'Praktische informatie'] as $text) {
            $this->assertStringContainsString($text, $html);
        }
    }

    public function test_the_schedule_puts_the_nights_between_the_journeys(): void
    {
        $this->assertSeeInOrder($this->render(), [
            'Heenreis',
            'Overnachten</strong> in Hotel Le Cro-Magnon, Les Eyzies',
            'Reisdag 2',
            'Overnachten</strong> in Guest house L&#039;Esprit des Chartrons, Bordeaux',
            'Terugreis',
            'Accommodaties',
        ]);
    }

    public function test_the_schedule_shows_each_leg_with_its_times_lines_and_ticket(): void
    {
        $this->assertSeeInOrder($this->render(), [
            'dinsdag 11 augustus',
            '<span>05:43</span><span class="legs-to"> – </span><span>06:37</span>',
            'Trein</strong> · Nijmegen → Utrecht Centraal',
            'NS Intercity <span class="muted">richting Den Helder</span>',
            '54 min',
            'Overstap</strong> · Utrecht Centraal',
            '11 min',
            'Nr. 9416 · Rijtuig 7 · Stoel 34',
            '3 u 7 min',
        ]);
    }

    public function test_the_accommodations_show_their_details_and_the_way_from_the_station(): void
    {
        $this->assertSeeInOrder($this->render(), [
            'Accommodaties',
            'Guest house L&#039;Esprit des Chartrons',
            'Guesthouse in Bordeaux',
            'vr 14 aug – zo 16 aug · 2 nachten',
            'Inchecken vanaf 16:00, uitchecken voor 11:30',
            '17 bis Rue Borie, 33300 Bordeaux',
            'Van het station naar de deur',
            'Tram</strong> · Bordeaux Saint-Jean',
            'Tram C <span class="muted">richting Parc des Expositions</span>',
            'Steek de trambaan over',
        ]);
    }

    public function test_the_practical_information_lists_the_pharmacies_of_the_places_with_one(): void
    {
        $html = $this->render();
        $practical = substr($html, (int) strpos($html, 'Praktische informatie'));

        $this->assertStringContainsString('Pharmacie de la Place Picard', $practical);
        $this->assertStringContainsString('Pharmacie de la Préhistoire', $practical);
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

        return view('travel::documents.pdf', ['document' => $documents->for($this->booking)])->render();
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
