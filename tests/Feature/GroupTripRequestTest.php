<?php

namespace Tests\Feature;

use App\Enums\Booking\Status as BookingStatus;
use App\Enums\Trip\TripFormat;
use App\Mail\TripRequestConfirmationMail;
use App\Mail\TripRequestNotificationMail;
use App\Models\Booking;
use App\Models\Departure;
use App\Models\Trip;
use App\Models\TripRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Requests for a group trip, made on one of its departures.
 */
class GroupTripRequestTest extends TestCase
{
    use RefreshDatabase;

    private Trip $trip;

    private Departure $departure;

    protected function setUp(): void
    {
        parent::setUp();

        $this->travelTo('2027-01-10 09:00:00');
        app()->setLocale('nl');
        Mail::fake();

        $this->trip = Trip::factory()->create(['format' => TripFormat::Group, 'published_at' => '2027-01-01', 'min_advance_days' => null]);
        $this->departure = Departure::factory()->for($this->trip)->create([
            'departs_on' => '2027-06-20',
            'returns_on' => '2027-06-26',
            'min_participants' => 4,
            'max_participants' => 8,
        ]);
    }

    private function payload(array $overrides = []): array
    {
        return [
            'name' => 'Anne de Vries',
            'email' => 'anne@example.com',
            'phone' => '0612345678',
            'departure_id' => $this->departure->id,
            'travelers_count' => 2,
            'single_room' => false,
            'notes' => 'Wij eten vegetarisch.',
            'consent_privacy' => true,
            ...$overrides,
        ];
    }

    private function request(array $overrides = [], ?Trip $trip = null): \Illuminate\Testing\TestResponse
    {
        return $this->post(route('trip-requests.store', $trip ?? $this->trip), $this->payload($overrides));
    }

    private function occupy(int $travelers, BookingStatus $status = BookingStatus::Confirmed): void
    {
        Booking::factory()->onDeparture($this->departure, $travelers)->create(['status' => $status]);
    }

    public function test_visitor_can_request_a_departure_of_a_group_trip(): void
    {
        $this->request(['single_room' => true])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('trips.show', $this->trip));

        $tripRequest = TripRequest::firstOrFail();
        $this->assertTrue($tripRequest->departure->is($this->departure));
        $this->assertSame(2, $tripRequest->travelers_count);
        $this->assertTrue($tripRequest->single_room);
        $this->assertFalse($tripRequest->interest_only);
    }

    public function test_request_for_a_group_trip_leaves_the_period_and_station_out(): void
    {
        $this->request([
            'preferred_month' => 6,
            'preferred_year' => 2027,
            'preferred_period_note' => 'Liefst juni',
            'departure_station' => 'Utrecht Centraal',
        ])->assertSessionHasNoErrors();

        $tripRequest = TripRequest::firstOrFail();
        $this->assertNull($tripRequest->preferred_month);
        $this->assertNull($tripRequest->preferred_period_note);
        $this->assertNull($tripRequest->departure_station);
    }

    public function test_request_reserves_no_place(): void
    {
        $this->occupy(4);

        $this->request(['travelers_count' => 3])->assertSessionHasNoErrors();

        $this->assertSame(4, $this->departure->fresh()->occupiedSeats());
    }

    public function test_request_needs_a_departure_of_this_trip_and_a_number_of_travelers(): void
    {
        $this->request(['departure_id' => null, 'travelers_count' => null])
            ->assertSessionHasErrors([
                'departure_id' => __('trip_request.validation.departure_required'),
                'travelers_count' => __('trip_request.validation.travelers_count_required'),
            ]);

        $other = Departure::factory()->create(['departs_on' => '2027-06-20', 'returns_on' => '2027-06-26']);
        $this->request(['departure_id' => $other->id])->assertSessionHasErrors('departure_id');

        $this->assertSame(0, TripRequest::count());
        Mail::assertNothingQueued();
        Mail::assertNothingSent();
    }

    public function test_group_of_more_than_six_cannot_request(): void
    {
        $this->request(['travelers_count' => 7])
            ->assertSessionHasErrors(['travelers_count' => __('trip_request.validation.travelers_count_max')]);
    }

    public function test_request_is_turned_down_kindly_when_the_departure_has_too_few_places_left(): void
    {
        $this->occupy(6);

        $this->request(['travelers_count' => 3])
            ->assertSessionHasErrors(['travelers_count' => 'Op dit vertrek is nog plek voor 2 reizigers.']);

        $this->occupy(1, BookingStatus::Pending);
        $this->request(['travelers_count' => 2])
            ->assertSessionHasErrors(['travelers_count' => 'Op dit vertrek is nog plek voor 1 reiziger.']);

        $this->assertSame(0, TripRequest::count());
    }

    public function test_request_on_a_departure_that_filled_up_says_interest_is_still_possible(): void
    {
        $this->occupy(8);

        $this->request()->assertSessionHasErrors(['departure_id' => __('trip_request.validation.departure_full')]);

        $this->assertSame(0, TripRequest::count());
    }

    public function test_visitor_can_leave_interest_in_a_full_departure(): void
    {
        $this->occupy(8);

        $this->request(['interest' => true, 'travelers_count' => 4])->assertSessionHasNoErrors();

        $tripRequest = TripRequest::firstOrFail();
        $this->assertTrue($tripRequest->interest_only);
        $this->assertTrue($tripRequest->departure->is($this->departure));
    }

    public function test_interest_in_a_departure_with_room_is_an_ordinary_request(): void
    {
        $this->request(['interest' => true])->assertSessionHasNoErrors();

        $this->assertFalse(TripRequest::firstOrFail()->interest_only);
    }

    public function test_departure_that_cannot_be_requested_anymore_is_refused(): void
    {
        $cancelled = Departure::factory()->for($this->trip)->cancelled()->create(['departs_on' => '2027-07-01', 'returns_on' => '2027-07-07']);
        $tooSoon = Departure::factory()->for($this->trip)->create(['departs_on' => '2027-01-25', 'returns_on' => '2027-01-31', 'guaranteed_at' => now()]);
        $this->trip->update(['min_advance_days' => 21]);

        foreach ([$cancelled, $tooSoon] as $departure) {
            $this->request(['departure_id' => $departure->id])
                ->assertSessionHasErrors(['departure_id' => __('trip_request.validation.departure_closed')]);
        }

        $this->assertSame(0, TripRequest::count());
    }

    public function test_individual_trip_is_requested_as_before_and_ignores_a_departure(): void
    {
        $individual = Trip::factory()->create(['published_at' => '2027-01-01']);

        $this->post(route('trip-requests.store', $individual), [
            'name' => 'Piet',
            'email' => 'piet@example.com',
            'preferred_month' => 9,
            'preferred_year' => 2027,
            'travelers_count' => 2,
            'departure_station' => 'Utrecht Centraal',
            'departure_id' => $this->departure->id,
            'single_room' => true,
            'consent_privacy' => true,
        ])->assertSessionHasNoErrors();

        $tripRequest = TripRequest::firstOrFail();
        $this->assertNull($tripRequest->departure_id);
        $this->assertFalse($tripRequest->single_room);
        $this->assertSame('2027-09', $tripRequest->preferred_month);
        $this->assertSame('Utrecht Centraal', $tripRequest->departure_station);
    }

    // Mails

    public function test_mails_name_the_departure_travelers_and_single_room(): void
    {
        $this->request(['single_room' => true])->assertSessionHasNoErrors();
        $tripRequest = TripRequest::with(['trip', 'departure'])->firstOrFail();

        $confirmation = new TripRequestConfirmationMail($tripRequest);
        $confirmation->assertSeeInHtml('20-06-2027 – 26-06-2027');
        $confirmation->assertSeeInHtml('Eenpersoonskamer');
        $confirmation->assertSeeInHtml(__('trip_request.mail.confirmation_group_note'));
        $confirmation->assertSeeInHtml(__('trip_request.mail.confirmation_what_to_expect_group_1'));
        $confirmation->assertDontSeeInHtml('Vertrekstation');
        $confirmation->assertDontSeeInHtml('Voorkeursperiode');
        $confirmation->assertSeeInText('Vertrek: 20-06-2027 – 26-06-2027');
        $confirmation->assertSeeInText('Eenpersoonskamer: Ja');

        $notification = new TripRequestNotificationMail($tripRequest);
        $notification->assertSeeInHtml('20-06-2027 – 26-06-2027');
        $notification->assertSeeInText('Aantal reizigers: 2');
        $notification->assertDontSeeInText('Vertrekstation');
    }

    public function test_mails_of_interest_in_a_full_departure_say_so(): void
    {
        $this->occupy(8);
        $this->request(['interest' => true])->assertSessionHasNoErrors();
        $tripRequest = TripRequest::with(['trip', 'departure'])->firstOrFail();

        (new TripRequestConfirmationMail($tripRequest))
            ->assertSeeInHtml(__('trip_request.mail.confirmation_interest_note'))
            ->assertDontSeeInHtml(__('trip_request.mail.confirmation_group_note'));
        (new TripRequestNotificationMail($tripRequest))
            ->assertSeeInText(__('trip_request.mail.interest_only'));
    }

    public function test_mails_of_an_individual_request_keep_the_period_and_station(): void
    {
        $tripRequest = TripRequest::factory()->for(Trip::factory(), 'trip')->create([
            'preferred_month' => '2027-09',
            'departure_station' => 'Utrecht Centraal',
            'travelers_count' => 3,
        ]);

        (new TripRequestNotificationMail($tripRequest))
            ->assertSeeInText('Voorkeursperiode: 2027-09')
            ->assertSeeInText('Vertrekstation: Utrecht Centraal')
            ->assertDontSeeInText('Eenpersoonskamer');
        (new TripRequestConfirmationMail($tripRequest))
            ->assertSeeInHtml(__('trip_request.mail.confirmation_what_to_expect_1'));
    }

    public function test_more_than_six_travelers_reads_as_such_in_the_mails(): void
    {
        $tripRequest = TripRequest::factory()->create(['travelers_count' => 7]);
        $legacy = TripRequest::factory()->create(['travelers_count' => 9]);

        $this->assertSame(__('trip_request.mail.notification_travelers_more_than_6'), $tripRequest->travelersLabel());
        $this->assertSame(__('trip_request.mail.notification_travelers_more_than_8'), $legacy->travelersLabel());
        (new TripRequestNotificationMail($tripRequest))->assertSeeInText('Meer dan 6');
    }
}
