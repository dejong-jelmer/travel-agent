<?php

namespace App\Travel\Tests\Feature;

use App\Models\Booking;
use App\Models\User;
use App\Travel\Enums\LegType;
use App\Travel\Enums\TransitMode;
use App\Travel\Models\Accommodation;
use App\Travel\Models\Journey;
use App\Travel\Models\Leg;
use App\Travel\Models\Station;
use App\Travel\Models\Stay;
use App\Travel\Models\TransitLine;
use App\Travel\Validation\Invariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRoutesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->admin()->create());
    }

    public function test_a_journey_is_added_updated_and_deleted(): void
    {
        $booking = Booking::factory()->create();

        $this->post(route('admin.bookings.journeys.store', $booking), ['label' => 'Heenreis', 'date' => '2026-08-11'])
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success');
        $journey = $booking->journeys()->sole();

        $this->put(route('admin.journeys.update', $journey), ['label' => 'Heenreis via Parijs', 'date' => '2026-08-11'])
            ->assertSessionHasNoErrors();
        $this->assertSame('Heenreis via Parijs', $journey->fresh()?->label);

        $this->delete(route('admin.journeys.destroy', $journey))->assertRedirect();
        $this->assertModelMissing($journey);
    }

    public function test_a_stay_is_added_updated_and_deleted(): void
    {
        $booking = Booking::factory()->create();
        $accommodation = Accommodation::factory()->create();

        $this->post(route('admin.bookings.stays.store', $booking), [
            'accommodation_id' => $accommodation->id,
            'arrival' => '2026-08-11',
            'departure' => '2026-08-14',
        ])->assertSessionHasNoErrors();
        $stay = $booking->stays()->sole();

        $this->put(route('admin.stays.update', $stay), [
            'accommodation_id' => $accommodation->id,
            'arrival' => '2026-08-11',
            'departure' => '2026-08-15',
        ])->assertSessionHasNoErrors();
        $this->assertSame(4, $stay->fresh()?->nights);

        $this->delete(route('admin.stays.destroy', $stay))->assertRedirect();
        $this->assertModelMissing($stay);
    }

    public function test_a_stay_that_departs_before_it_arrives_gets_the_message_on_departure(): void
    {
        $booking = Booking::factory()->create();

        $this->post(route('admin.bookings.stays.store', $booking), [
            'accommodation_id' => Accommodation::factory()->create()->id,
            'arrival' => '2026-08-14',
            'departure' => '2026-08-11',
        ])->assertSessionHasErrors(['departure' => Invariant::DatesRunForward->message()]);

        $this->assertDatabaseCount('travel_stays', 0);
    }

    public function test_a_leg_is_added_to_a_journey_with_its_lines(): void
    {
        $journey = Journey::factory()->create();
        $intercity = TransitLine::factory()->create(['name' => 'NS Intercity']);

        $this->post(route('admin.journeys.legs.store', $journey), [
            'type' => LegType::Train->value,
            'from_station_id' => Station::factory()->create()->id,
            'to_station_id' => Station::factory()->create()->id,
            'departure_at' => '2026-08-11 05:43',
            'arrival_at' => '2026-08-11 06:37',
            'transit_lines' => [['id' => $intercity->id, 'direction' => 'Den Helder']],
        ])->assertSessionHasNoErrors();

        $leg = $journey->legs()->sole();
        $this->assertSame(0, $leg->position);
        $this->assertSame(54, $leg->duration);
        $this->assertSame('Den Helder', $leg->transitLines->sole()->pivot->direction);
    }

    public function test_a_leg_that_breaks_an_invariant_gets_the_message_on_its_field(): void
    {
        $journey = Journey::factory()->create();
        $metro = TransitLine::factory()->create(['mode' => TransitMode::Metro]);

        $this->post(route('admin.journeys.legs.store', $journey), [
            'type' => LegType::Transfer->value,
            'from_station_id' => Station::factory()->create()->id,
            'to_station_id' => Station::factory()->create()->id,
        ])->assertSessionHasErrors(['to_station_id' => Invariant::TransferStaysInOnePlace->message()]);

        $this->post(route('admin.journeys.legs.store', $journey), [
            'type' => LegType::Tram->value,
            'transit_lines' => [['id' => $metro->id, 'direction' => 'Bagneux']],
        ])->assertSessionHasErrors(['transit_lines' => Invariant::LinesMatchTheLeg->message()]);

        $this->post(route('admin.journeys.legs.store', $journey), [
            'type' => LegType::Train->value,
            'departure_at' => '2026-08-11 10:00',
            'arrival_at' => '2026-08-11 09:00',
            'duration_minutes' => 30,
        ])->assertSessionHasErrors([
            'arrival_at' => Invariant::TimeRunsForward->message(),
            'duration_minutes' => Invariant::TimesSetTheDuration->message(),
        ]);

        $this->post(route('admin.accommodations.legs.store', Accommodation::factory()->create()), [
            'type' => LegType::Walk->value,
            'departure_at' => '2026-08-14 09:00',
        ])->assertSessionHasErrors(['departure_at' => Invariant::ArrivalRouteHasNoClock->message()]);

        $this->assertDatabaseCount('travel_legs', 0);
    }

    public function test_a_leg_is_updated_and_deleted(): void
    {
        $leg = Leg::factory()->walk()->accommodationRoute()->create();

        $this->put(route('admin.legs.update', $leg), [
            'type' => LegType::Walk->value,
            'duration_minutes' => 3,
            'note' => 'laatste 100m lopen, hotel aan de rechterhand',
        ])->assertSessionHasNoErrors();
        $this->assertSame(3, $leg->fresh()?->duration);

        $this->delete(route('admin.legs.destroy', $leg))->assertRedirect();
        $this->assertModelMissing($leg);
    }

    public function test_the_legs_of_a_journey_are_reordered(): void
    {
        $journey = Journey::factory()->create();
        $legs = Leg::factory()->walk()->for($journey, 'legable')->count(2)
            ->sequence(['position' => 0], ['position' => 1])
            ->create();

        $this->patchJson(route('admin.journeys.legs.order', $journey), ['ids' => [$legs[0]->id]])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('ids');

        $this->patchJson(route('admin.journeys.legs.order', $journey), ['ids' => [$legs[1]->id, $legs[0]->id]])
            ->assertOk()
            ->assertJson(['success' => true]);
        $this->assertSame([$legs[1]->id, $legs[0]->id], $journey->legs()->pluck('id')->all());
    }

    public function test_the_journeys_and_stays_of_a_booking_are_reordered(): void
    {
        $booking = Booking::factory()->create();
        $journeys = Journey::factory()->for($booking)->count(2)->sequence(['position' => 0], ['position' => 1])->create();
        $stays = Stay::factory()->for($booking)->count(2)->sequence(['position' => 0], ['position' => 1])->create();

        $this->patchJson(route('admin.bookings.journeys.order', $booking), ['ids' => [$journeys[1]->id, $journeys[0]->id]])
            ->assertOk();
        $this->patchJson(route('admin.bookings.stays.order', $booking), ['ids' => [$stays[1]->id, $stays[0]->id]])
            ->assertOk();

        $this->assertSame([$journeys[1]->id, $journeys[0]->id], $booking->journeys()->pluck('id')->all());
        $this->assertSame([$stays[1]->id, $stays[0]->id], $booking->stays()->pluck('id')->all());
    }

    public function test_a_non_admin_cannot_write(): void
    {
        $booking = Booking::factory()->create();
        $this->actingAs(User::factory()->create(['role' => 'guest']));

        $this->post(route('admin.bookings.journeys.store', $booking), ['label' => 'Heenreis'])->assertForbidden();

        $this->assertDatabaseCount('travel_journeys', 0);
    }
}
