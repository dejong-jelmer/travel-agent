<?php

namespace Tests\Feature;

use App\Enums\Booking\Status as BookingStatus;
use App\Enums\Trip\DepartureStatus;
use App\Enums\Trip\TripFormat;
use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\BookingChange;
use App\Models\Departure;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class DepartureAdminTest extends TestCase
{
    use RefreshDatabase;

    private Trip $trip;

    protected function setUp(): void
    {
        parent::setUp();

        $this->travelTo('2027-01-10 09:00:00');
        $this->actingAs(User::factory()->admin()->create());
        $this->trip = Trip::factory()->create(['format' => TripFormat::Group]);
    }

    private function payload(array $overrides = []): array
    {
        return [
            'departs_on' => '2027-06-20',
            'returns_on' => '2027-06-26',
            'min_participants' => 6,
            'max_participants' => 12,
            'price_per_person' => '1295.50',
            'single_supplement' => '150',
            'decision_date' => null,
            ...$overrides,
        ];
    }

    private function departure(array $attributes = []): Departure
    {
        return Departure::factory()->for($this->trip)->create([
            'departs_on' => '2027-06-20',
            'returns_on' => '2027-06-26',
            'min_participants' => 4,
            'max_participants' => 8,
            ...$attributes,
        ]);
    }

    private function book(Departure $departure, BookingStatus $status, int $adults = 2): Booking
    {
        return Booking::factory()->onDeparture($departure, $adults)->create(['status' => $status]);
    }

    // Overview

    public function test_admin_sees_the_departures_of_a_trip_with_their_counts_and_status(): void
    {
        $later = $this->departure(['departs_on' => '2027-09-01', 'returns_on' => '2027-09-07']);
        $sooner = $this->departure();
        $this->book($sooner, BookingStatus::Confirmed, 4);
        $this->book($sooner, BookingStatus::Pending, 1);
        $this->book($sooner, BookingStatus::Canceled, 2);
        $this->departure(['trip_id' => Trip::factory()->create(['format' => TripFormat::Group])->id]);

        $this->get(route('admin.trips.departures.index', $this->trip))->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Admin/Trip/Departures/Index')
                ->where('isGroupTrip', true)
                ->where('trip.name', $this->trip->name)
                ->has('departures', 2)
                ->where('departures.0.id', $sooner->id)
                ->where('departures.0.departs_on', '2027-06-20')
                ->where('departures.0.duration_in_days', 7)
                ->where('departures.0.occupied_seats', 5)
                ->where('departures.0.confirmed_participants', 4)
                ->where('departures.0.available_seats', 3)
                ->where('departures.0.has_reached_minimum', true)
                ->where('departures.0.awaits_guarantee', true)
                ->where('departures.0.decision_overdue', false)
                ->where('departures.0.status.value', DepartureStatus::Open->value)
                ->where('departures.0.latest_decision_date', '2027-05-31')
                ->where('departures.1.id', $later->id)
                ->where('departures.1.awaits_guarantee', false)
        );
    }

    public function test_overview_loads_the_counts_of_all_departures_in_one_query(): void
    {
        foreach (range(1, 4) as $month) {
            $this->book($this->departure(['departs_on' => "2027-0{$month}-20", 'returns_on' => "2027-0{$month}-26"]), BookingStatus::Confirmed);
        }

        DB::enableQueryLog();
        $this->get(route('admin.trips.departures.index', $this->trip))->assertOk();
        $departureQueries = collect(DB::getQueryLog())->filter(fn (array $query) => str_contains($query['query'], '`departures`'));
        // The participants are summed inside the departures query, not counted per departure afterwards
        $countQueries = collect(DB::getQueryLog())->filter(fn (array $query) => str_contains($query['query'], 'total_adults'));
        DB::disableQueryLog();

        $this->assertCount(1, $departureQueries);
        $this->assertCount(1, $countQueries);
        $this->assertSame($departureQueries->keys()->all(), $countQueries->keys()->all());
    }

    public function test_overview_marks_a_departure_whose_decision_is_overdue(): void
    {
        $this->departure(['decision_date' => '2027-01-09']);

        $this->get(route('admin.trips.departures.index', $this->trip))->assertInertia(
            fn (AssertableInertia $page) => $page->where('departures.0.decision_overdue', true)
        );
    }

    public function test_overview_of_an_individual_trip_says_it_is_not_a_group_trip(): void
    {
        $individual = Trip::factory()->create();

        $this->get(route('admin.trips.departures.index', $individual))->assertInertia(
            fn (AssertableInertia $page) => $page->where('isGroupTrip', false)->has('departures', 0)
        );
    }

    public function test_non_admin_cannot_manage_departures(): void
    {
        $departure = $this->departure();
        $this->actingAs(User::factory()->create(['role' => UserRole::Guest]));

        $this->get(route('admin.trips.departures.index', $this->trip))->assertForbidden();
        $this->post(route('admin.trips.departures.store', $this->trip), $this->payload())->assertForbidden();
        $this->put(route('admin.departures.update', $departure), $this->payload())->assertForbidden();
        $this->post(route('admin.departures.guarantee', $departure))->assertForbidden();
        $this->post(route('admin.departures.cancel', $departure), ['reason' => 'Strike'])->assertForbidden();
        $this->delete(route('admin.departures.destroy', $departure))->assertForbidden();

        $this->assertNull($departure->refresh()->guaranteed_at);
    }

    // All trips

    public function test_overview_of_all_trips_puts_what_needs_attention_first(): void
    {
        $otherTrip = Trip::factory()->create(['format' => TripFormat::Group]);

        $quiet = $this->departure(['departs_on' => '2027-03-01', 'returns_on' => '2027-03-07']);
        $minimumReached = $this->departure(['departs_on' => '2027-09-01', 'returns_on' => '2027-09-07']);
        $this->book($minimumReached, BookingStatus::Confirmed, 4);
        $overdue = $this->departure(['trip_id' => $otherTrip->id, 'decision_date' => '2027-01-08']);
        $dueSoon = $this->departure(['decision_date' => '2027-01-15']);
        $guaranteed = $this->departure(['decision_date' => '2027-01-08', 'guaranteed_at' => now()]);
        $cancelled = $this->departure(['cancelled_at' => now(), 'cancellation_reason' => 'Strike']);
        $completed = $this->departure(['departs_on' => '2026-12-01', 'returns_on' => '2026-12-07']);

        $this->get(route('admin.departures.index'))->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Admin/Departures/Index')
                // The decision that is due the soonest first
                ->where('attention', fn ($attention) => collect($attention)->pluck('id')->all() === [$overdue->id, $dueSoon->id, $minimumReached->id])
                ->where('attention.0.decision_overdue', true)
                ->where('attention.0.trip.name', $otherTrip->name)
                ->where('attention.1.decision_due_soon', true)
                ->where('attention.1.days_until_decision', 5)
                ->where('attention.2.awaits_guarantee', true)
                ->where('upcoming', fn ($upcoming) => collect($upcoming)->pluck('id')->all() === [$quiet->id, $guaranteed->id])
                ->where('upcoming.0.needs_attention', false)
                ->where('past', fn ($past) => collect($past)->pluck('id')->sort()->values()->all() === [$cancelled->id, $completed->id])
        );
    }

    public function test_decision_is_only_due_soon_inside_the_warning_period_and_below_the_minimum(): void
    {
        $outside = $this->departure(['decision_date' => '2027-01-18']);
        $onTheEdge = $this->departure(['decision_date' => '2027-01-17']);
        $today = $this->departure(['decision_date' => '2027-01-10']);
        $reached = $this->departure(['decision_date' => '2027-01-12']);
        $this->book($reached, BookingStatus::Confirmed, 4);

        $this->assertFalse($outside->isDecisionDueSoon());
        $this->assertTrue($onTheEdge->isDecisionDueSoon());
        $this->assertTrue($today->isDecisionDueSoon());
        $this->assertFalse($today->isDecisionOverdue());
        $this->assertFalse($reached->isDecisionDueSoon());
        $this->assertTrue($reached->awaitsGuarantee());

        config(['departures.decision_warning_days' => 14]);
        $this->assertTrue($outside->isDecisionDueSoon());
    }

    public function test_overview_of_all_trips_loads_the_counts_in_the_departure_queries(): void
    {
        foreach (range(1, 4) as $month) {
            $this->book($this->departure(['departs_on' => "2027-0{$month}-20", 'returns_on' => "2027-0{$month}-26"]), BookingStatus::Confirmed);
        }

        DB::enableQueryLog();
        $this->get(route('admin.departures.index'))->assertOk();
        $countQueries = collect(DB::getQueryLog())->filter(fn (array $query) => str_contains($query['query'], 'total_adults'));
        DB::disableQueryLog();

        // One for the upcoming departures, one for the past ones, however many departures there are
        $this->assertCount(2, $countQueries);
    }

    public function test_participant_list_shows_every_booking_with_its_travelers(): void
    {
        $departure = $this->departure();
        $confirmed = Booking::factory()->onDeparture($departure, adults: 2, children: 1)->withTravelers(adults: 2, children: 1)
            ->create(['status' => BookingStatus::Confirmed, 'single_rooms' => 1]);
        $canceled = Booking::factory()->onDeparture($departure, adults: 1)->withTravelers(adults: 1, children: 0)
            ->create(['status' => BookingStatus::Canceled]);
        $new = Booking::factory()->onDeparture($departure, adults: 1)->withTravelers(adults: 1, children: 0)
            ->create(['status' => BookingStatus::New]);
        $this->book($this->departure(), BookingStatus::Confirmed);
        $traveler = $confirmed->travelers()->first();
        $traveler->update(['special_requests' => 'Glutenvrij', 'special_requests_consent' => true]);

        $this->get(route('admin.departures.show', $departure))->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Admin/Departures/Show')
                ->where('departure.id', $departure->id)
                ->where('departure.trip.name', $this->trip->name)
                ->where('departure.occupied_seats', 4)
                ->where('departure.confirmed_participants', 3)
                // The canceled booking goes last
                ->where('bookings', fn ($bookings) => collect($bookings)->pluck('id')->all() === [$confirmed->id, $new->id, $canceled->id])
                ->where('bookings.0.reference', $confirmed->reference)
                ->where('bookings.0.participants', 3)
                ->where('bookings.0.single_rooms', 1)
                ->where('bookings.0.occupies_seat', true)
                ->where('bookings.0.counts_towards_minimum', true)
                ->has('bookings.0.travelers', 3)
                ->where('bookings.0.travelers', fn ($travelers) => collect($travelers)->contains(
                    fn ($row) => $row['id'] === $traveler->id && $row['special_requests'] === 'Glutenvrij'
                ))
                ->where('bookings.1.counts_towards_minimum', false)
                ->where('bookings.2.occupies_seat', false)
        );
    }

    public function test_dashboard_lists_the_departures_that_need_attention(): void
    {
        $this->departure(['departs_on' => '2027-03-01', 'returns_on' => '2027-03-07']);
        $overdue = $this->departure(['decision_date' => '2027-01-08']);
        $this->departure(['cancelled_at' => now(), 'cancellation_reason' => 'Strike', 'decision_date' => '2027-01-08']);

        $this->get(route('admin.dashboard'))->assertInertia(
            fn (AssertableInertia $page) => $page
                ->where('departures.upcoming', 2)
                ->has('departures.attention', 1)
                ->where('departures.attention.0.id', $overdue->id)
                ->where('departures.attention.0.trip.name', $this->trip->name)
        );
    }

    public function test_non_admin_cannot_see_the_overview_or_a_participant_list(): void
    {
        $departure = $this->departure();
        $this->actingAs(User::factory()->create(['role' => UserRole::Guest]));

        $this->get(route('admin.departures.index'))->assertForbidden();
        $this->get(route('admin.departures.show', $departure))->assertForbidden();
    }

    // Creating

    public function test_admin_can_create_a_departure(): void
    {
        $this->post(route('admin.trips.departures.store', $this->trip), $this->payload())
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success', __('departure.created'));

        $departure = $this->trip->departures()->firstOrFail();
        $this->assertSame('2027-06-20', $departure->departs_on->toDateString());
        $this->assertSame('2027-06-26', $departure->returns_on->toDateString());
        $this->assertSame(6, $departure->min_participants);
        $this->assertSame(12, $departure->max_participants);
        $this->assertSame(129550, $departure->price_per_person);
        $this->assertSame(15000, $departure->single_supplement);
        $this->assertSame('2027-05-31', $departure->decision_date->toDateString());
        $this->assertNull($departure->guaranteed_at);
    }

    public function test_empty_prices_fall_back_on_the_trip_and_zero_stays_zero(): void
    {
        $this->post(route('admin.trips.departures.store', $this->trip), $this->payload(['price_per_person' => null, 'single_supplement' => '0']))
            ->assertSessionHasNoErrors();

        $departure = $this->trip->departures()->firstOrFail();
        $this->assertNull($departure->price_per_person);
        $this->assertSame(0, $departure->single_supplement);
    }

    public function test_admin_can_set_an_earlier_decision_date(): void
    {
        $this->post(route('admin.trips.departures.store', $this->trip), $this->payload(['decision_date' => '2027-05-01']))
            ->assertSessionHasNoErrors();

        $this->assertSame('2027-05-01', $this->trip->departures()->firstOrFail()->decision_date->toDateString());
    }

    public function test_decision_date_cannot_be_later_than_the_law_allows(): void
    {
        $this->post(route('admin.trips.departures.store', $this->trip), $this->payload(['decision_date' => '2027-06-01']))
            ->assertSessionHasErrors(['decision_date' => __('departure.validation.decision_date_too_late', ['date' => '31-05-2027'])]);

        // On the last day itself it is allowed
        $this->post(route('admin.trips.departures.store', $this->trip), $this->payload(['decision_date' => '2027-05-31']))
            ->assertSessionHasNoErrors();
    }

    public function test_departure_within_the_legal_term_has_to_be_guaranteed_from_the_start(): void
    {
        // A week-long trip that leaves in 15 days: the 20 days are already gone
        $payload = $this->payload(['departs_on' => '2027-01-25', 'returns_on' => '2027-01-31']);

        $this->post(route('admin.trips.departures.store', $this->trip), $payload)
            ->assertSessionHasErrors(['decision_date' => __('departure.validation.deadline_passed', ['date' => '05-01-2027'])]);
        $this->assertSame(0, Departure::count());

        $this->post(route('admin.trips.departures.store', $this->trip), [...$payload, 'guaranteed' => true])
            ->assertSessionHasNoErrors();
        $this->assertTrue(Departure::firstOrFail()->isGuaranteed());
    }

    public function test_departure_can_be_created_guaranteed(): void
    {
        $this->post(route('admin.trips.departures.store', $this->trip), $this->payload(['guaranteed' => true]))
            ->assertSessionHasNoErrors();

        $this->assertSame(DepartureStatus::Guaranteed, Departure::firstOrFail()->status());
    }

    public function test_departure_needs_valid_dates_and_numbers(): void
    {
        $this->post(route('admin.trips.departures.store', $this->trip), $this->payload([
            'departs_on' => '2027-01-10',
            'returns_on' => '2027-01-09',
            'min_participants' => 0,
            'max_participants' => 5,
            'price_per_person' => -1,
        ]))->assertSessionHasErrors(['departs_on', 'returns_on', 'min_participants', 'price_per_person']);

        $this->post(route('admin.trips.departures.store', $this->trip), $this->payload(['min_participants' => 8, 'max_participants' => 6]))
            ->assertSessionHasErrors('max_participants');

        $this->assertSame(0, Departure::count());
    }

    public function test_individual_trip_cannot_get_departures(): void
    {
        $individual = Trip::factory()->create();

        $this->post(route('admin.trips.departures.store', $individual), $this->payload())
            ->assertSessionHasErrors(['departs_on' => __('departure.validation.not_a_group_trip')]);

        $this->assertSame(0, Departure::count());
    }

    // Updating

    public function test_admin_can_update_a_departure(): void
    {
        $departure = $this->departure(['decision_date' => '2027-05-01']);

        $this->put(route('admin.departures.update', $departure), $this->payload(['max_participants' => 14, 'decision_date' => '2027-05-10']))
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success', __('departure.updated'));

        $departure->refresh();
        $this->assertSame(14, $departure->max_participants);
        $this->assertSame(129550, $departure->price_per_person);
        $this->assertSame('2027-05-10', $departure->decision_date->toDateString());
    }

    public function test_maximum_cannot_go_below_the_occupied_seats(): void
    {
        $departure = $this->departure();
        $this->book($departure, BookingStatus::Confirmed, 4);
        $this->book($departure, BookingStatus::New, 3);

        $this->put(route('admin.departures.update', $departure), $this->payload(['min_participants' => 2, 'max_participants' => 6]))
            ->assertSessionHasErrors(['max_participants' => __('departure.validation.max_below_occupied', ['occupied' => 7])]);

        $this->put(route('admin.departures.update', $departure), $this->payload(['min_participants' => 2, 'max_participants' => 7]))
            ->assertSessionHasNoErrors();
        $this->assertTrue($departure->refresh()->isFull());
    }

    public function test_lowering_the_minimum_below_the_confirmed_participants_does_not_guarantee_by_itself(): void
    {
        $departure = $this->departure(['min_participants' => 6]);
        $this->book($departure, BookingStatus::Confirmed, 4);

        $this->put(route('admin.departures.update', $departure), $this->payload(['min_participants' => 4]))
            ->assertSessionHasNoErrors();

        $departure->refresh();
        $this->assertTrue($departure->hasReachedMinimum());
        $this->assertFalse($departure->isGuaranteed());
    }

    public function test_changing_the_dates_moves_the_bookings_along_and_logs_it(): void
    {
        $departure = $this->departure();
        $booking = $this->book($departure, BookingStatus::Confirmed);
        $canceled = $this->book($departure, BookingStatus::Canceled);
        $elsewhere = $this->book($this->departure(), BookingStatus::Confirmed);

        $this->put(route('admin.departures.update', $departure), $this->payload(['departs_on' => '2027-07-01', 'returns_on' => '2027-07-08']))
            ->assertSessionHasNoErrors();

        foreach ([$booking, $canceled] as $moved) {
            $this->assertSame('2027-07-01', $moved->refresh()->departure_date->toDateString());
            $this->assertSame('2027-07-08', $moved->return_date->toDateString());
        }
        $this->assertSame('2027-06-20', $elsewhere->refresh()->departure_date->toDateString());

        $change = BookingChange::where('booking_id', $booking->id)->where('field', 'departure_date')->firstOrFail();
        $this->assertStringContainsString('2027-07-01', $change->new_value);
        $this->assertSame(auth()->id(), $change->user_id);
        $this->assertSame(0, BookingChange::where('booking_id', $elsewhere->id)->count());
    }

    public function test_update_without_new_dates_leaves_the_bookings_alone(): void
    {
        $departure = $this->departure();
        $booking = $this->book($departure, BookingStatus::Confirmed);

        $this->put(route('admin.departures.update', $departure), $this->payload(['max_participants' => 10]))
            ->assertSessionHasNoErrors();

        $this->assertSame(0, BookingChange::where('booking_id', $booking->id)->count());
    }

    public function test_cancelled_or_completed_departure_cannot_be_updated(): void
    {
        $cancelled = $this->departure(['cancelled_at' => now(), 'cancellation_reason' => 'Strike']);
        $completed = $this->departure(['departs_on' => '2026-12-01', 'returns_on' => '2026-12-07']);

        foreach ([$cancelled, $completed] as $departure) {
            $this->put(route('admin.departures.update', $departure), $this->payload(['max_participants' => 20]))
                ->assertSessionHasErrors(['departs_on' => __('departure.validation.not_editable')]);
            $this->assertSame(8, $departure->refresh()->max_participants);
        }
    }

    // Guaranteeing

    public function test_admin_can_guarantee_a_departure_by_hand(): void
    {
        $departure = $this->departure();

        $this->post(route('admin.departures.guarantee', $departure))->assertSessionHas('success', __('departure.guaranteed'));

        $this->assertSame('2027-01-10 09:00:00', $departure->refresh()->guaranteed_at->toDateTimeString());
        $this->assertSame(DepartureStatus::Guaranteed, $departure->status());
    }

    public function test_cancelled_departure_cannot_be_guaranteed_from_the_admin(): void
    {
        $departure = $this->departure(['cancelled_at' => now(), 'cancellation_reason' => 'Strike']);

        $this->post(route('admin.departures.guarantee', $departure))
            ->assertSessionHas('error', __('departure.validation.not_guaranteeable'));

        $this->assertNull($departure->refresh()->guaranteed_at);
    }

    // Cancelling

    public function test_cancelling_a_departure_needs_a_reason(): void
    {
        $departure = $this->departure();

        $this->post(route('admin.departures.cancel', $departure), ['reason' => ''])->assertSessionHasErrors('reason');

        $this->assertFalse($departure->refresh()->isCancelled());
    }

    public function test_cancelling_a_departure_cancels_its_bookings_and_logs_each_one(): void
    {
        $departure = $this->departure();
        $new = $this->book($departure, BookingStatus::New);
        $confirmed = $this->book($departure, BookingStatus::Confirmed);
        $alreadyCanceled = $this->book($departure, BookingStatus::Canceled);
        $elsewhere = $this->book($this->departure(), BookingStatus::Confirmed);

        $this->post(route('admin.departures.cancel', $departure), ['reason' => 'Te weinig deelnemers'])
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success', trans_choice('departure.cancelled', 2, ['count' => 2]));

        $departure->refresh();
        $this->assertSame('2027-01-10 09:00:00', $departure->cancelled_at->toDateTimeString());
        $this->assertSame('Te weinig deelnemers', $departure->cancellation_reason);
        $this->assertSame(DepartureStatus::Cancelled, $departure->status());
        $this->assertSame(0, $departure->occupiedSeats());

        $this->assertSame(BookingStatus::Canceled, $new->refresh()->status);
        $this->assertSame(BookingStatus::Canceled, $confirmed->refresh()->status);
        $this->assertSame(BookingStatus::Confirmed, $elsewhere->refresh()->status);

        $change = BookingChange::where('booking_id', $confirmed->id)->where('field', 'status')->firstOrFail();
        $this->assertSame(BookingStatus::Confirmed->value, $change->old_value);
        $this->assertSame(BookingStatus::Canceled->value, $change->new_value);
        $this->assertSame(auth()->id(), $change->user_id);
        $this->assertSame(0, BookingChange::where('booking_id', $alreadyCanceled->id)->count());
    }

    public function test_guaranteed_departure_can_still_be_cancelled_and_keeps_its_guarantee_moment(): void
    {
        $departure = $this->departure(['guaranteed_at' => '2027-01-05 12:00:00', 'decision_date' => '2027-01-01']);

        $this->post(route('admin.departures.cancel', $departure), ['reason' => 'Overmacht: spoorstaking'])
            ->assertSessionHasNoErrors();

        $departure->refresh();
        $this->assertSame(DepartureStatus::Cancelled, $departure->status());
        $this->assertNotNull($departure->guaranteed_at);
    }

    public function test_departure_cannot_be_cancelled_twice_or_after_it_has_left(): void
    {
        $cancelled = $this->departure(['cancelled_at' => '2027-01-02 08:00:00', 'cancellation_reason' => 'Strike']);
        $left = $this->departure(['departs_on' => '2027-01-10', 'returns_on' => '2027-01-16']);

        foreach ([$cancelled, $left] as $departure) {
            $this->post(route('admin.departures.cancel', $departure), ['reason' => 'Nog een keer'])
                ->assertSessionHasErrors(['reason' => __('departure.validation.not_cancellable')]);
        }

        $this->assertSame('Strike', $cancelled->refresh()->cancellation_reason);
        $this->assertFalse($left->refresh()->isCancelled());
    }

    // Deleting

    public function test_admin_can_delete_a_departure_without_bookings(): void
    {
        $departure = $this->departure();

        $this->delete(route('admin.departures.destroy', $departure))->assertSessionHas('success', __('departure.deleted'));

        $this->assertModelMissing($departure);
    }

    public function test_departure_with_bookings_is_kept_also_when_they_are_canceled_or_deleted(): void
    {
        $withCanceled = $this->departure();
        $this->book($withCanceled, BookingStatus::Canceled);
        $withDeleted = $this->departure();
        $this->book($withDeleted, BookingStatus::New)->delete();

        foreach ([$withCanceled, $withDeleted] as $departure) {
            $this->delete(route('admin.departures.destroy', $departure))
                ->assertSessionHas('error', __('departure.validation.has_bookings'));
            $this->assertModelExists($departure);
        }
    }
}
