<?php

namespace App\Travel\Tests\Feature;

use App\Models\Booking;
use App\Models\User;
use App\Travel\Database\Seeders\SampleTripSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class TravelPlanPageTest extends TestCase
{
    use RefreshDatabase;

    private Booking $booking;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SampleTripSeeder::class);
        $this->booking = Booking::where('internal_notes', SampleTripSeeder::BOOKING_NOTE)->sole();
        $this->actingAs(User::factory()->admin()->create());
    }

    public function test_it_shows_the_journeys_with_their_legs_in_order(): void
    {
        $this->get(route('admin.bookings.travel-plan', $this->booking))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Admin/Travel/Plan')
                ->where('booking.reference', $this->booking->reference)
                ->where('booking.departure_date', '2026-08-11')
                ->where('journeys.0.label', 'Heenreis')
                ->where('journeys.0.date', '2026-08-11')
                ->where('journeys.2.label', 'Terugreis')
                ->has('journeys', 3)
                ->has('journeys.0.legs', 11)
                ->has('journeys.0.legs.0', fn (AssertableInertia $leg) => $leg
                    ->whereType('id', 'integer')
                    ->where('type', 'train')
                    ->where('from_station.name', 'Nijmegen')
                    ->where('to_station.name', 'Utrecht Centraal')
                    ->where('departure_at', '2026-08-11T05:43')
                    ->where('arrival_at', '2026-08-11T06:37')
                    ->where('duration', 54)
                    ->where('transit_lines.0.name', 'NS Intercity')
                    ->where('transit_lines.0.direction', 'Den Helder')
                    ->etc())
                ->where('journeys.0.legs.1.type', 'transfer')
                ->where('journeys.0.legs.1.departure_at', null)
                ->where('journeys.0.legs.1.duration', 11)
                ->where('journeys.0.legs.4.number', '9416')
                ->where('journeys.0.legs.4.seat', '34'));
    }

    public function test_it_shows_the_stays_with_their_accommodation_and_arrival_route(): void
    {
        $this->get(route('admin.bookings.travel-plan', $this->booking))
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('stays.0.accommodation.name', 'Hotel Le Cro-Magnon')
                ->where('stays.0.arrival', '2026-08-11')
                ->where('stays.0.nights', 3)
                ->where('stays.1.accommodation.type', 'guest_house')
                ->where('stays.1.accommodation.city', 'Bordeaux')
                ->where('stays.1.accommodation.check_in', '16:00')
                ->has('stays.1.accommodation.arrival_route', 3)
                ->where('stays.1.accommodation.arrival_route.0.transit_lines.0.name', 'Tram C')
                ->where('stays.1.accommodation.arrival_route.0.transit_lines.0.direction', 'Parc des Expositions')
                ->where('stays.1.accommodation.arrival_route.0.transit_lines.1.name', 'Tram D')
                ->where('stays.1.accommodation.arrival_route.2.type', 'walk'));
    }

    public function test_it_shows_the_cities_of_the_trip_with_their_pharmacies(): void
    {
        $this->get(route('admin.bookings.travel-plan', $this->booking))
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->has('cities', 7)
                ->where('cities.0.name', 'Nijmegen')
                ->has('cities.0.pharmacies', 0)
                ->where('cities.4.name', 'Bordeaux')
                ->where('cities.4.pharmacies.0.name', 'Pharmacie de la Place Picard'));
    }

    public function test_it_passes_the_dutch_labels_of_the_types_and_which_leg_types_carry_lines(): void
    {
        $this->get(route('admin.bookings.travel-plan', $this->booking))
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('legTypeOptions.0', ['id' => 'train', 'name' => 'Trein', 'has_lines' => true])
                ->where('legTypeOptions.5', ['id' => 'taxi', 'name' => 'Taxi', 'has_lines' => false])
                ->where('legTypeOptions.6', ['id' => 'transfer', 'name' => 'Overstap', 'has_lines' => false])
                ->where('accommodationTypeOptions.2', ['id' => 'guest_house', 'name' => 'Guesthouse']));
    }

    public function test_it_passes_the_stored_duration_for_the_form(): void
    {
        $this->get(route('admin.bookings.travel-plan', $this->booking))
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('journeys.0.legs.0.duration', 54)
                ->where('journeys.0.legs.0.duration_minutes', null)
                ->where('journeys.0.legs.1.duration_minutes', 11));
    }

    public function test_it_passes_the_library_to_pick_from(): void
    {
        $this->get(route('admin.bookings.travel-plan', $this->booking))
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->has('stations', 8)
                ->where('stations.0.name', 'Bordeaux Saint-Jean')
                ->where('stations.0.city', 'Bordeaux')
                ->has('transitLines', 9)
                ->where('transitLines.0.name', 'Eurostar')
                ->where('transitLines.0.mode', 'train')
                ->has('accommodations', 2)
                ->where('accommodations.0', fn ($accommodation) => $accommodation['name'] === "Guest house L'Esprit des Chartrons"
                    && $accommodation['city'] === 'Bordeaux'));
    }

    public function test_a_booking_without_a_plan_shows_empty_lists(): void
    {
        $this->get(route('admin.bookings.travel-plan', Booking::factory()->create()))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->has('journeys', 0)
                ->has('stays', 0)
                ->has('cities', 0));
    }

    public function test_it_loads_the_plan_without_lazy_loading(): void
    {
        $this->assertNoLazyLoading(fn () => $this->get(route('admin.bookings.travel-plan', $this->booking))->assertOk());
    }

    public function test_only_an_admin_sees_the_plan(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'guest']));
        $this->get(route('admin.bookings.travel-plan', $this->booking))->assertForbidden();

        auth()->logout();
        $this->get(route('admin.bookings.travel-plan', $this->booking))->assertRedirect('/admin/login');
    }
}
