<?php

namespace Tests\Feature;

use App\Enums\Booking\Status as BookingStatus;
use App\Enums\Trip\TripFormat;
use App\Enums\TripRequest\Status;
use App\Models\Booking;
use App\Models\Departure;
use App\Models\Trip;
use App\Models\TripRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

/**
 * Requests for a group trip as the admin sees them, on the request and on its departure.
 */
class DepartureRequestAdminTest extends TestCase
{
    use RefreshDatabase;

    private Trip $trip;

    private Departure $departure;

    protected function setUp(): void
    {
        parent::setUp();

        $this->travelTo('2027-01-10 09:00:00');
        $this->actingAs(User::factory()->admin()->create());

        $this->trip = Trip::factory()->create(['format' => TripFormat::Group]);
        $this->departure = Departure::factory()->for($this->trip)->create([
            'departs_on' => '2027-06-20',
            'returns_on' => '2027-06-26',
            'min_participants' => 4,
            'max_participants' => 8,
        ]);
    }

    private function tripRequest(array $attributes = []): TripRequest
    {
        return TripRequest::factory()->for($this->trip)->create([
            'departure_id' => $this->departure->id,
            'travelers_count' => 2,
            'preferred_month' => null,
            'departure_station' => null,
            'status' => Status::New,
            ...$attributes,
        ]);
    }

    public function test_request_page_shows_the_departure_as_it_stands_now(): void
    {
        Booking::factory()->onDeparture($this->departure, 5)->create(['status' => BookingStatus::Confirmed]);
        $tripRequest = $this->tripRequest(['single_room' => true, 'interest_only' => true]);

        $this->get(route('admin.trip-requests.edit', $tripRequest))->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Admin/Request/Edit')
                ->where('departure.id', $this->departure->id)
                ->where('departure.departs_on', '2027-06-20')
                ->where('departure.available_seats', 3)
                ->where('tripRequest.single_room', true)
                ->where('tripRequest.interest_only', true)
                ->where('travelersLabel', '2')
        );
    }

    public function test_request_page_of_an_individual_trip_has_no_departure(): void
    {
        $tripRequest = TripRequest::factory()->create(['travelers_count' => 7]);

        $this->get(route('admin.trip-requests.edit', $tripRequest))->assertInertia(
            fn (AssertableInertia $page) => $page
                ->where('departure', null)
                ->where('travelersLabel', __('trip_request.mail.notification_travelers_more_than_6'))
        );
    }

    public function test_request_list_sends_the_departure_and_interest_along(): void
    {
        $this->tripRequest(['interest_only' => true]);

        $this->get(route('admin.trip-requests.index'))->assertInertia(
            fn (AssertableInertia $page) => $page
                ->where('tripRequests.data.0.departure.id', $this->departure->id)
                ->where('tripRequests.data.0.interest_only', true)
        );
    }

    public function test_departure_counts_the_places_asked_by_open_requests_apart_from_interest(): void
    {
        $this->tripRequest(['travelers_count' => 2]);
        $this->tripRequest(['travelers_count' => 3, 'status' => Status::Contacted]);
        $this->tripRequest(['travelers_count' => 4, 'interest_only' => true]);
        $this->tripRequest(['travelers_count' => 1, 'interest_only' => true]);
        // Handled requests count no more
        $this->tripRequest(['travelers_count' => 5, 'status' => Status::Converted]);
        $this->tripRequest(['travelers_count' => 5, 'status' => Status::Archived]);
        TripRequest::factory()->create(['travelers_count' => 6]);

        $this->get(route('admin.trips.departures.index', $this->trip))->assertInertia(
            fn (AssertableInertia $page) => $page
                ->where('departures.0.requested_seats', 5)
                ->where('departures.0.interested_requests', 2)
                // Requests reserve nothing
                ->where('departures.0.occupied_seats', 0)
        );
    }

    public function test_request_counts_of_all_departures_load_without_a_query_per_departure(): void
    {
        foreach (range(1, 4) as $month) {
            $departure = Departure::factory()->for($this->trip)->create(['departs_on' => "2027-0{$month}-20", 'returns_on' => "2027-0{$month}-26"]);
            TripRequest::factory()->for($this->trip)->create(['departure_id' => $departure->id, 'status' => Status::New]);
        }

        DB::enableQueryLog();
        $this->get(route('admin.departures.index'))->assertOk();
        // The admin menu counts the new requests apart, so only the queries that read requests per departure count
        $requestQueries = collect(DB::getQueryLog())->filter(
            fn (array $query) => str_contains($query['query'], '`trip_requests`') && str_contains($query['query'], '`departures`')
        );
        $standalone = collect(DB::getQueryLog())->filter(
            fn (array $query) => str_contains($query['query'], 'from `trip_requests` where `trip_requests`.`departure_id`')
        );
        DB::disableQueryLog();

        // Inside the queries for the upcoming and the past departures
        $this->assertCount(2, $requestQueries);
        $this->assertCount(0, $standalone);
    }

    public function test_departure_page_lists_its_open_requests_with_waiting_ones_last(): void
    {
        $waiting = $this->tripRequest(['interest_only' => true, 'name' => 'Wacht Op Plek']);
        $asking = $this->tripRequest(['single_room' => true, 'name' => 'Anne de Vries', 'travelers_count' => 3]);
        $this->tripRequest(['status' => Status::Converted]);
        TripRequest::factory()->create();

        $this->get(route('admin.departures.show', $this->departure))->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Admin/Departures/Show')
                ->has('tripRequests', 2)
                ->where('tripRequests.0.id', $asking->id)
                ->where('tripRequests.0.travelers', '3')
                ->where('tripRequests.0.single_room', true)
                ->where('tripRequests.1.id', $waiting->id)
                ->where('tripRequests.1.interest_only', true)
                ->where('departure.requested_seats', 3)
                ->where('departure.interested_requests', 1)
        );
    }

    public function test_deleting_a_departure_keeps_its_requests(): void
    {
        $tripRequest = $this->tripRequest();

        $this->delete(route('admin.departures.destroy', $this->departure))->assertSessionHas('success');

        $this->assertNull($tripRequest->fresh()->departure_id);
    }
}
