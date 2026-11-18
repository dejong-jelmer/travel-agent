<?php

namespace Tests\Feature;

use App\Enums\Booking\Status as BookingStatus;
use App\Enums\Trip\DepartureStatus;
use App\Models\Booking;
use App\Models\Departure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DepartureStatusTest extends TestCase
{
    use RefreshDatabase;

    private function departure(array $attributes = []): Departure
    {
        return Departure::factory()->create(['min_participants' => 4, 'max_participants' => 8, ...$attributes]);
    }

    private function book(Departure $departure, BookingStatus $status, int $adults = 2, int $children = 0): Booking
    {
        return Booking::factory()->onDeparture($departure, $adults, $children)->create(['status' => $status]);
    }

    // Participant counts

    public function test_departure_without_bookings_has_no_participants(): void
    {
        $departure = $this->departure();

        $this->assertSame(0, $departure->occupiedSeats());
        $this->assertSame(0, $departure->confirmedParticipants());
        $this->assertSame(8, $departure->availableSeats());
    }

    public function test_every_booking_that_is_not_canceled_occupies_seats(): void
    {
        $departure = $this->departure();
        $this->book($departure, BookingStatus::New, 1);
        $this->book($departure, BookingStatus::Pending, 1);
        $this->book($departure, BookingStatus::Confirmed, 1);
        $this->book($departure, BookingStatus::Completed, 1);
        $this->book($departure, BookingStatus::Canceled, 3);

        $this->assertSame(4, $departure->occupiedSeats());
        $this->assertSame(4, $departure->availableSeats());
    }

    public function test_only_confirmed_and_completed_bookings_count_towards_the_minimum(): void
    {
        $departure = $this->departure();
        $this->book($departure, BookingStatus::New, 2);
        $this->book($departure, BookingStatus::Pending, 2);
        $this->book($departure, BookingStatus::Confirmed, 1);
        $this->book($departure, BookingStatus::Completed, 2);
        $this->book($departure, BookingStatus::Canceled, 1);

        $this->assertSame(3, $departure->confirmedParticipants());
    }

    public function test_children_count_as_participants(): void
    {
        $departure = $this->departure();
        $this->book($departure, BookingStatus::Confirmed, adults: 2, children: 2);

        $this->assertSame(4, $departure->occupiedSeats());
        $this->assertSame(4, $departure->confirmedParticipants());
    }

    public function test_deleted_bookings_and_bookings_of_other_departures_are_not_counted(): void
    {
        $departure = $this->departure();
        $this->book($departure, BookingStatus::Confirmed, 2);
        $this->book($departure, BookingStatus::Confirmed, 3)->delete();
        $this->book($this->departure(), BookingStatus::Confirmed, 5);

        $this->assertSame(2, $departure->occupiedSeats());
        $this->assertSame(2, $departure->confirmedParticipants());
    }

    public function test_scope_loads_the_same_counts_without_a_query_per_departure(): void
    {
        $departures = collect(range(1, 5))->map(function (int $travelers) {
            $departure = $this->departure();
            $this->book($departure, BookingStatus::Confirmed, $travelers);
            $this->book($departure, BookingStatus::Pending, 1);
            $this->book($departure, BookingStatus::Canceled, 2);

            return $departure;
        });
        $empty = $this->departure();

        DB::enableQueryLog();

        $loaded = Departure::withParticipantCounts()->orderBy('id')->get();
        $counts = $loaded->map(fn (Departure $departure) => [
            $departure->occupiedSeats(),
            $departure->confirmedParticipants(),
            $departure->availableSeats(),
            $departure->status(),
        ]);

        $this->assertCount(1, DB::getQueryLog());
        DB::disableQueryLog();

        $this->assertSame([2, 1, 6, DepartureStatus::Open], $counts[0]);
        $this->assertSame([6, 5, 2, DepartureStatus::Open], $counts[4]);
        $this->assertSame([0, 0, 8, DepartureStatus::Open], $counts[5]);
        $this->assertTrue($loaded[5]->is($empty));

        foreach ($departures as $index => $departure) {
            $this->assertSame($departure->occupiedSeats(), $counts[$index][0]);
            $this->assertSame($departure->confirmedParticipants(), $counts[$index][1]);
        }
    }

    // Status

    public function test_new_departure_is_open(): void
    {
        $this->assertSame(DepartureStatus::Open, $this->departure()->status());
    }

    public function test_departure_is_full_when_the_occupied_seats_reach_the_maximum(): void
    {
        $departure = $this->departure(['max_participants' => 4]);
        $this->book($departure, BookingStatus::New, 3);

        $this->assertFalse($departure->isFull());
        $this->assertSame(DepartureStatus::Open, $departure->status());

        // Places that are held but not confirmed fill the departure as well
        $this->book($departure, BookingStatus::Pending, 1);

        $this->assertTrue($departure->isFull());
        $this->assertSame(0, $departure->availableSeats());
        $this->assertSame(DepartureStatus::Full, $departure->status());
    }

    public function test_full_departure_opens_up_again_when_a_booking_is_canceled(): void
    {
        $departure = $this->departure(['max_participants' => 4]);
        $booking = $this->book($departure, BookingStatus::Confirmed, 4);
        $this->assertSame(DepartureStatus::Full, $departure->status());

        $booking->update(['status' => BookingStatus::Canceled]);

        $this->assertSame(DepartureStatus::Open, $departure->status());
    }

    public function test_reaching_the_minimum_does_not_guarantee_a_departure_by_itself(): void
    {
        $departure = $this->departure();
        $this->book($departure, BookingStatus::Confirmed, 4);

        $this->assertTrue($departure->hasReachedMinimum());
        $this->assertFalse($departure->refresh()->isGuaranteed());
        $this->assertSame(DepartureStatus::Open, $departure->status());
    }

    public function test_pending_bookings_do_not_reach_the_minimum(): void
    {
        $departure = $this->departure();
        $this->book($departure, BookingStatus::Pending, 4);
        $this->book($departure, BookingStatus::Confirmed, 3);

        $this->assertFalse($departure->hasReachedMinimum());
    }

    public function test_guaranteeing_sets_the_moment_and_the_status(): void
    {
        $departure = $this->departure();

        $this->travelTo('2027-03-01 10:00:00', fn () => $departure->guarantee());

        $this->assertSame('2027-03-01 10:00:00', $departure->refresh()->guaranteed_at->toDateTimeString());
        $this->assertSame(DepartureStatus::Guaranteed, $departure->status());
    }

    public function test_departure_can_be_guaranteed_below_the_minimum(): void
    {
        $departure = $this->departure();
        $this->book($departure, BookingStatus::Confirmed, 1);

        $departure->guarantee();

        $this->assertSame(DepartureStatus::Guaranteed, $departure->refresh()->status());
    }

    public function test_guarantee_stands_when_bookings_are_canceled_afterwards(): void
    {
        $departure = $this->departure();
        $first = $this->book($departure, BookingStatus::Confirmed, 2);
        $second = $this->book($departure, BookingStatus::Confirmed, 2);
        $departure->guarantee();
        $guaranteedAt = $departure->refresh()->guaranteed_at;

        $first->update(['status' => BookingStatus::Canceled]);
        $second->delete();

        $departure->refresh();
        $this->assertFalse($departure->hasReachedMinimum());
        $this->assertTrue($departure->isGuaranteed());
        $this->assertTrue($guaranteedAt->equalTo($departure->guaranteed_at));
        $this->assertSame(DepartureStatus::Guaranteed, $departure->status());
    }

    public function test_guaranteeing_again_keeps_the_first_moment(): void
    {
        $departure = $this->departure();
        $this->travelTo('2027-03-01 10:00:00', fn () => $departure->guarantee());
        $this->travelTo('2027-04-01 10:00:00', fn () => $departure->guarantee());

        $this->assertSame('2027-03-01 10:00:00', $departure->refresh()->guaranteed_at->toDateTimeString());
    }

    public function test_cancelled_departure_cannot_be_guaranteed(): void
    {
        $departure = Departure::factory()->cancelled()->create();

        $this->expectException(\DomainException::class);

        $departure->guarantee();
    }

    public function test_guaranteed_departure_that_is_full_has_the_status_full_and_stays_guaranteed(): void
    {
        $departure = Departure::factory()->guaranteed()->create(['min_participants' => 2, 'max_participants' => 4]);
        $this->book($departure, BookingStatus::Confirmed, 4);

        $this->assertSame(DepartureStatus::Full, $departure->status());
        $this->assertTrue($departure->isGuaranteed());
    }

    public function test_cancelled_goes_before_every_other_status(): void
    {
        $departure = Departure::factory()->guaranteed()->cancelled()->create([
            'departs_on' => today()->subDays(10),
            'returns_on' => today()->subDays(3),
            'max_participants' => 2,
        ]);
        $this->book($departure, BookingStatus::Confirmed, 2);

        $this->assertSame(DepartureStatus::Cancelled, $departure->status());
        $this->assertFalse($departure->isCompleted());
    }

    public function test_departure_is_completed_once_the_day_of_return_has_passed(): void
    {
        $departure = Departure::factory()->guaranteed()->create([
            'departs_on' => '2027-06-20',
            'returns_on' => '2027-06-26',
            'max_participants' => 2,
        ]);
        $this->book($departure, BookingStatus::Confirmed, 2);

        $this->travelTo('2027-06-26 23:00:00');
        $this->assertSame(DepartureStatus::Full, $departure->status());

        $this->travelTo('2027-06-27 00:30:00');
        $this->assertSame(DepartureStatus::Completed, $departure->status());
    }

    public function test_status_has_a_dutch_label(): void
    {
        app()->setLocale('nl');

        $this->assertSame(
            ['Open', 'Gegarandeerd', 'Vol', 'Geannuleerd', 'Afgerond'],
            array_map(fn (DepartureStatus $status) => $status->label(), DepartureStatus::cases())
        );
    }

    // Relations

    public function test_booking_knows_its_departure_and_an_individual_booking_has_none(): void
    {
        $departure = $this->departure();

        $this->assertTrue($this->book($departure, BookingStatus::New)->departure->is($departure));
        $this->assertNull(Booking::factory()->create()->departure);
        $this->assertCount(1, $departure->bookings);
    }

    public function test_departure_with_bookings_cannot_be_deleted(): void
    {
        $departure = $this->departure();
        $this->book($departure, BookingStatus::Canceled)->delete();

        $this->expectException(\Illuminate\Database\QueryException::class);

        $departure->delete();
    }
}
