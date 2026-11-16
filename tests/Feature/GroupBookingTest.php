<?php

namespace Tests\Feature;

use App\Enums\Booking\CostCategory;
use App\Enums\Booking\PaymentStatus;
use App\Enums\Booking\Status;
use App\Enums\SettingKey;
use App\Enums\TravelerType;
use App\Enums\Trip\TripFormat;
use App\Events\BookingFailed;
use App\Events\DepartureMinimumReached;
use App\Mail\AdminDepartureMinimumReachedMail;
use App\Models\Booking;
use App\Models\BookingChange;
use App\Models\BookingContact;
use App\Models\BookingTraveler;
use App\Models\Departure;
use App\Models\Setting;
use App\Models\Trip;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class GroupBookingTest extends TestCase
{
    use RefreshDatabase;

    private Trip $trip;

    private Departure $departure;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->admin()->create());

        $this->trip = Trip::factory()->withPrices()->create(['format' => TripFormat::Group]);
        $this->departure = Departure::factory()->for($this->trip)->create([
            'departs_on' => today()->addMonths(3),
            'returns_on' => today()->addMonths(3)->addDays(7),
            'min_participants' => 4,
            'max_participants' => 6,
            'price_per_person' => 120000,
            'single_supplement' => 15000,
        ]);
    }

    // Creating a booking on a departure

    public function test_booking_of_a_group_trip_takes_its_departure_and_dates(): void
    {
        $this->post(route('admin.bookings.store'), $this->payload(adults: 2, children: 1, overrides: [
            'departure_date' => today()->addYear()->toDateString(),
            'return_date' => today()->addYear()->addDays(3)->toDateString(),
        ]))->assertSessionHasNoErrors();

        $booking = Booking::firstOrFail();

        $this->assertTrue($booking->departure->is($this->departure));
        $this->assertTrue($booking->trip->is($this->trip));
        $this->assertTrue($booking->departure_date->isSameDay($this->departure->departs_on));
        $this->assertTrue($booking->return_date->isSameDay($this->departure->returns_on));
        $this->assertSame(3, $this->departure->occupiedSeats());
    }

    public function test_booking_of_a_group_trip_needs_a_departure(): void
    {
        $this->post(route('admin.bookings.store'), $this->payload(overrides: [
            'departure_id' => null,
            'departure_date' => today()->addMonth()->toDateString(),
        ]))->assertSessionHasErrors(['departure_id' => __('departure.booking.required')]);

        $this->assertSame(0, Booking::count());
    }

    public function test_booking_is_rejected_on_a_departure_of_another_trip(): void
    {
        $other = Departure::factory()->create();

        $this->post(route('admin.bookings.store'), $this->payload(overrides: ['departure_id' => $other->id]))
            ->assertSessionHasErrors('departure_id');

        $this->assertSame(0, Booking::count());
    }

    public function test_booking_of_an_individual_trip_cannot_have_a_departure(): void
    {
        $individual = Trip::factory()->withPrices()->create();

        $this->post(route('admin.bookings.store'), $this->payload(overrides: [
            'trip' => ['id' => $individual->id],
            'departure_date' => today()->addMonths(2)->toDateString(),
        ]))->assertSessionHasErrors('departure_id');

        $this->assertSame(0, Booking::count());
    }

    public function test_booking_of_an_individual_trip_is_unchanged(): void
    {
        $individual = Trip::factory()->withPrices()->create(['blocked_dates' => null]);
        $date = today()->addMonths(2);

        $this->post(route('admin.bookings.store'), $this->payload(overrides: [
            'trip' => ['id' => $individual->id],
            'departure_id' => null,
            'departure_date' => $date->toDateString(),
            'return_date' => $date->copy()->addDays(5)->toDateString(),
        ]))->assertSessionHasNoErrors();

        $booking = Booking::firstOrFail();
        $this->assertNull($booking->departure_id);
        $this->assertTrue($booking->departure_date->isSameDay($date));
        $this->assertTrue($booking->return_date->isSameDay($date->copy()->addDays(5)));
    }

    // Capacity

    public function test_booking_can_take_the_last_places(): void
    {
        $this->occupy(4);

        $this->post(route('admin.bookings.store'), $this->payload(adults: 2))->assertSessionHasNoErrors();

        $this->assertSame(6, $this->departure->occupiedSeats());
        $this->assertTrue($this->departure->isFull());
    }

    public function test_booking_is_rejected_when_it_needs_more_places_than_are_free(): void
    {
        $this->occupy(4);
        Event::fake([BookingFailed::class]);

        $this->post(route('admin.bookings.store'), $this->payload(adults: 2, children: 1))
            ->assertSessionHasErrors(['departure_id' => __('departure.booking.not_enough_seats', ['available' => 2, 'requested' => 3])]);

        $this->assertSame(1, Booking::count());
        $this->assertSame(4, $this->departure->occupiedSeats());
        Event::assertNotDispatched(BookingFailed::class);
    }

    public function test_booking_is_rejected_on_a_full_departure(): void
    {
        $this->occupy(6, Status::Pending);

        $this->post(route('admin.bookings.store'), $this->payload(adults: 1))
            ->assertSessionHasErrors(['departure_id' => __('departure.booking.full')]);
    }

    public function test_canceled_and_deleted_bookings_free_their_places(): void
    {
        $this->occupy(3, Status::Canceled);
        $this->occupy(3)->delete();

        $this->post(route('admin.bookings.store'), $this->payload(adults: 4, children: 2))->assertSessionHasNoErrors();

        $this->assertSame(6, $this->departure->occupiedSeats());
    }

    public function test_booking_is_rejected_on_a_cancelled_departure(): void
    {
        $this->departure->update(['cancelled_at' => now(), 'cancellation_reason' => 'Strike']);

        $this->post(route('admin.bookings.store'), $this->payload())
            ->assertSessionHasErrors(['departure_id' => __('departure.booking.cancelled')]);
    }

    public function test_booking_is_rejected_on_a_departure_that_has_left(): void
    {
        $this->departure->update(['departs_on' => today(), 'returns_on' => today()->addDays(7)]);

        $this->post(route('admin.bookings.store'), $this->payload())
            ->assertSessionHasErrors(['departure_id' => __('departure.booking.departed')]);
    }

    public function test_places_taken_between_the_form_check_and_the_save_are_not_given_out_twice(): void
    {
        $this->occupy(4);
        Event::fake([BookingFailed::class]);

        // The form request reads the departure first and finds two places free. A competing booking takes them
        // right after that check; the service has to notice when it reads the departure again under its lock.
        $reads = 0;
        Departure::retrieved(function () use (&$reads) {
            if (++$reads === 2) {
                Booking::factory()->onDeparture($this->departure, adults: 2)->create(['status' => Status::New]);
            }
        });
        DB::enableQueryLog();

        $response = $this->post(route('admin.bookings.store'), $this->payload(adults: 2));

        $lockingReads = collect(DB::getQueryLog())->filter(
            fn (array $query) => str_contains($query['query'], 'from `departures`') && str_contains($query['query'], 'for update')
        );
        DB::disableQueryLog();

        $response->assertSessionHasErrors(['departure_id' => __('departure.booking.full')]);
        $this->assertCount(1, $lockingReads);
        $this->assertSame(2, Booking::count());
        $this->assertSame(6, $this->departure->occupiedSeats());
        $this->assertSame(0, BookingTraveler::count());
        Event::assertNotDispatched(BookingFailed::class);
    }

    // Updating a booking on a departure

    public function test_canceled_booking_cannot_be_taken_back_when_its_places_are_gone(): void
    {
        $booking = $this->createBooking(adults: 3);
        $booking->update(['status' => Status::Canceled]);
        $this->occupy(5);

        $this->put(route('admin.bookings.update', $booking), $this->updatePayload($booking, ['status' => Status::Confirmed->value]))
            ->assertSessionHasErrors(['status' => __('departure.booking.not_enough_seats', ['available' => 1, 'requested' => 3])]);

        $this->assertSame(Status::Canceled, $booking->refresh()->status);
    }

    public function test_canceled_booking_can_be_taken_back_while_there_is_room(): void
    {
        $booking = $this->createBooking(adults: 3);
        $booking->update(['status' => Status::Canceled]);
        $this->occupy(3);

        $this->put(route('admin.bookings.update', $booking), $this->updatePayload($booking, ['status' => Status::Pending->value]))
            ->assertSessionHasNoErrors();

        $this->assertSame(Status::Pending, $booking->refresh()->status);
        $this->assertTrue($this->departure->isFull());
    }

    public function test_booking_on_a_full_departure_can_still_be_updated(): void
    {
        $booking = $this->createBooking(adults: 2);
        $this->occupy(4);

        $this->put(route('admin.bookings.update', $booking), $this->updatePayload($booking, ['status' => Status::Confirmed->value]))
            ->assertSessionHasNoErrors();

        $this->assertSame(Status::Confirmed, $booking->refresh()->status);
    }

    public function test_booking_on_a_departure_keeps_the_dates_of_the_departure(): void
    {
        $booking = $this->createBooking();

        $this->put(route('admin.bookings.update', $booking), $this->updatePayload($booking, [
            'return_date' => $this->departure->returns_on->copy()->addDays(4)->toDateString(),
        ]))->assertSessionHasNoErrors();

        $this->assertTrue($booking->refresh()->return_date->isSameDay($this->departure->returns_on));
    }

    public function test_status_change_of_a_group_booking_is_logged_as_a_booking_change(): void
    {
        $booking = $this->createBooking();

        $this->put(route('admin.bookings.update', $booking), $this->updatePayload($booking, ['status' => Status::Canceled->value]));

        $change = BookingChange::where('booking_id', $booking->id)->where('field', 'status')->firstOrFail();
        $this->assertSame(Status::New->value, $change->old_value);
        $this->assertSame(Status::Canceled->value, $change->new_value);
        $this->assertSame(auth()->id(), $change->user_id);
    }

    // The mail when the minimum is reached

    public function test_admin_is_told_when_a_booking_brings_the_departure_to_its_minimum(): void
    {
        Event::fake([DepartureMinimumReached::class]);
        $this->occupy(2, Status::Confirmed);
        $booking = $this->createBooking(adults: 2);

        $this->put(route('admin.bookings.update', $booking), $this->updatePayload($booking, ['status' => Status::Confirmed->value]))
            ->assertSessionHasNoErrors();

        Event::assertDispatchedTimes(DepartureMinimumReached::class, 1);
        Event::assertDispatched(DepartureMinimumReached::class, fn ($event) => $event->departure->is($this->departure));
        $this->assertFalse($this->departure->refresh()->isGuaranteed());
    }

    public function test_admin_is_not_told_while_the_minimum_is_not_reached(): void
    {
        Event::fake([DepartureMinimumReached::class]);
        $this->occupy(1, Status::Confirmed);
        $this->occupy(2, Status::Pending);
        $booking = $this->createBooking(adults: 2);

        $this->put(route('admin.bookings.update', $booking), $this->updatePayload($booking, ['status' => Status::Confirmed->value]));

        Event::assertNotDispatched(DepartureMinimumReached::class);
    }

    public function test_admin_is_not_told_again_when_the_minimum_was_already_reached(): void
    {
        Event::fake([DepartureMinimumReached::class]);
        $this->occupy(4, Status::Confirmed);
        $booking = $this->createBooking(adults: 1);

        $this->put(route('admin.bookings.update', $booking), $this->updatePayload($booking, ['status' => Status::Confirmed->value]));

        Event::assertNotDispatched(DepartureMinimumReached::class);
    }

    public function test_admin_is_not_told_about_a_departure_that_is_already_guaranteed(): void
    {
        Event::fake([DepartureMinimumReached::class]);
        $this->departure->guarantee();
        $this->occupy(2, Status::Confirmed);
        $booking = $this->createBooking(adults: 2);

        $this->put(route('admin.bookings.update', $booking), $this->updatePayload($booking, ['status' => Status::Confirmed->value]));

        Event::assertNotDispatched(DepartureMinimumReached::class);
    }

    public function test_admin_is_told_again_when_the_minimum_is_reached_a_second_time(): void
    {
        Event::fake([DepartureMinimumReached::class]);
        $this->occupy(2, Status::Confirmed);
        $booking = $this->createBooking(adults: 2);

        foreach ([Status::Confirmed, Status::Canceled, Status::Confirmed] as $status) {
            $this->put(route('admin.bookings.update', $booking), $this->updatePayload($booking, ['status' => $status->value]))
                ->assertSessionHasNoErrors();
        }

        Event::assertDispatchedTimes(DepartureMinimumReached::class, 2);
    }

    public function test_minimum_reached_mail_goes_to_the_booking_address(): void
    {
        Mail::fake();
        config(['booking.mail' => 'boekingen@example.com']);
        $this->occupy(2, Status::Confirmed);
        $booking = $this->createBooking(adults: 2);

        $this->put(route('admin.bookings.update', $booking), $this->updatePayload($booking, ['status' => Status::Confirmed->value]));

        Mail::assertSent(AdminDepartureMinimumReachedMail::class, function (AdminDepartureMinimumReachedMail $mail) {
            return $mail->hasTo('boekingen@example.com') && $mail->departure->is($this->departure);
        });
    }

    public function test_minimum_reached_mail_shows_the_counts_and_says_the_departure_is_not_guaranteed(): void
    {
        app()->setLocale('nl');
        $this->occupy(4, Status::Confirmed);
        $this->occupy(1, Status::Pending);

        $mail = new AdminDepartureMinimumReachedMail($this->departure);

        $mail->assertHasSubject('Minimum gehaald: '.$this->trip->name.' op '.$this->departure->departs_on->format('d-m-Y'));
        $mail->assertSeeInHtml('4 deelnemers, minimum 4');
        $mail->assertSeeInHtml('5 van 6 plekken');
        $mail->assertSeeInHtml('nog niet gegarandeerd');
        $mail->assertSeeInText('4 deelnemers, minimum 4');
        $mail->assertSeeInText(route('admin.trips.show', $this->trip));
    }

    // Price

    public function test_booking_on_a_departure_is_sold_at_the_price_of_the_departure(): void
    {
        $this->setFees();

        $booking = $this->createBooking(adults: 2, overrides: ['travelers' => [
            'adults' => $this->travelers(2, TravelerType::Adult),
            'children' => $this->travelers(1, TravelerType::Child),
        ]]);

        $this->assertSame(120000, $booking->price_per_person);
        $this->assertSame(15000, $booking->single_supplement);
        $this->assertSame(0, $booking->single_rooms);
        $this->assertSame(360000, $booking->base_total_price);
        $this->assertSame(363750, $booking->grand_total_price);
        $this->assertSame(363750, $booking->calculated_price);
        $this->assertSame(363750, $booking->display_price);
        $this->assertNull($booking->trip_price_id);
    }

    public function test_booking_on_a_departure_needs_no_cost_items_or_margin(): void
    {
        $payload = $this->payload();
        unset($payload['cost_items'], $payload['margin_percentage']);

        $this->post(route('admin.bookings.store'), $payload)->assertSessionHasNoErrors();

        $booking = Booking::firstOrFail();
        $this->assertCount(0, $booking->costItems);
        $this->assertSame(240000, $booking->calculated_price);
    }

    public function test_cost_items_of_a_booking_on_a_departure_do_not_change_its_price(): void
    {
        $booking = $this->createBooking(adults: 2);

        $this->assertCount(1, $booking->costItems);
        $this->assertSame(240000, $booking->calculated_price);
    }

    public function test_booking_of_an_individual_trip_still_needs_cost_items_and_a_margin(): void
    {
        $individual = Trip::factory()->withPrices()->create();
        $payload = $this->payload(overrides: [
            'trip' => ['id' => $individual->id],
            'departure_id' => null,
            'departure_date' => today()->addMonths(2)->toDateString(),
        ]);
        unset($payload['cost_items'], $payload['margin_percentage']);

        $this->post(route('admin.bookings.store'), $payload)->assertSessionHasErrors(['cost_items', 'margin_percentage']);
    }

    public function test_traveler_on_their_own_gets_a_single_room_unless_told_otherwise(): void
    {
        $solo = $this->createBooking(adults: 1);
        $sharing = $this->createBooking(adults: 1, overrides: ['single_rooms' => 0]);

        $this->assertSame(1, $solo->single_rooms);
        $this->assertSame(135000, $solo->calculated_price);
        $this->assertSame(0, $sharing->single_rooms);
        $this->assertSame(120000, $sharing->calculated_price);
    }

    public function test_booking_pays_the_supplement_for_every_single_room(): void
    {
        $booking = $this->createBooking(adults: 3, overrides: ['single_rooms' => 1]);

        $this->assertSame(1, $booking->single_rooms);
        $this->assertSame(375000, $booking->calculated_price);
    }

    public function test_booking_cannot_have_more_single_rooms_than_adults(): void
    {
        $this->post(route('admin.bookings.store'), $this->payload(adults: 2, children: 1, overrides: ['single_rooms' => 3]))
            ->assertSessionHasErrors('single_rooms');

        $this->assertSame(0, Booking::count());
    }

    public function test_final_price_overrides_the_price_of_the_departure(): void
    {
        $booking = $this->createBooking(adults: 2, overrides: ['final_price' => 200000]);

        $this->assertSame(240000, $booking->calculated_price);
        $this->assertSame(200000, $booking->display_price);
    }

    public function test_changing_the_single_rooms_recalculates_the_price_from_the_snapshot(): void
    {
        $this->setFees();
        $booking = $this->createBooking(adults: 2);
        $this->departure->update(['price_per_person' => 999900, 'single_supplement' => 99900]);

        $this->put(route('admin.bookings.update', $booking), $this->updatePayload($booking, ['single_rooms' => 2]))
            ->assertSessionHasNoErrors();

        $booking->refresh();
        $this->assertSame(2, $booking->single_rooms);
        $this->assertSame(120000, $booking->price_per_person);
        $this->assertSame(273750, $booking->calculated_price);
        $this->assertSame(273750, $booking->grand_total_price);
        $this->assertNotNull(BookingChange::where('booking_id', $booking->id)->where('field', 'single_rooms')->first());
    }

    public function test_updating_a_booking_without_single_rooms_keeps_them_and_the_price(): void
    {
        $booking = $this->createBooking(adults: 1);
        $payload = $this->updatePayload($booking, ['status' => Status::Confirmed->value]);
        unset($payload['cost_items'], $payload['margin_percentage']);

        $this->put(route('admin.bookings.update', $booking), $payload)->assertSessionHasNoErrors();

        $booking->refresh();
        $this->assertSame(1, $booking->single_rooms);
        $this->assertSame(135000, $booking->calculated_price);
    }

    public function test_changing_the_price_of_the_departure_leaves_existing_bookings_untouched(): void
    {
        $booking = $this->createBooking(adults: 2);

        $this->departure->update(['price_per_person' => 150000]);
        $later = $this->createBooking(adults: 2);

        $this->assertSame(240000, $booking->refresh()->calculated_price);
        $this->assertSame(300000, $later->calculated_price);
    }

    public function test_departure_without_its_own_price_is_booked_at_the_price_of_the_trip(): void
    {
        $this->trip->prices()->delete();
        $tripPrice = $this->trip->prices()->create([
            'valid_from' => today(),
            'valid_until' => today()->addYear(),
            'base_price_pp' => 90000,
            'single_supplement' => 10000,
            'label' => 'high_season',
        ]);
        $this->departure->update(['price_per_person' => null, 'single_supplement' => null]);

        $booking = $this->createBooking(adults: 1);

        $this->assertSame(90000, $booking->price_per_person);
        $this->assertSame(10000, $booking->single_supplement);
        $this->assertSame(100000, $booking->calculated_price);
        $this->assertSame($tripPrice->id, $booking->trip_price_id);
    }

    public function test_booking_is_rejected_on_a_departure_without_any_price(): void
    {
        $this->trip->prices()->delete();
        $this->departure->update(['price_per_person' => null]);
        Event::fake([BookingFailed::class]);

        $this->post(route('admin.bookings.store'), $this->payload())
            ->assertSessionHasErrors(['departure_id' => __('departure.booking.no_price')]);

        $this->assertSame(0, Booking::count());
        Event::assertNotDispatched(BookingFailed::class);
    }

    public function test_single_rooms_are_ignored_for_an_individual_trip(): void
    {
        $individual = Trip::factory()->withPrices()->create(['blocked_dates' => null]);

        $this->post(route('admin.bookings.store'), $this->payload(overrides: [
            'trip' => ['id' => $individual->id],
            'departure_id' => null,
            'departure_date' => today()->addMonths(2)->toDateString(),
            'single_rooms' => 2,
        ]))->assertSessionHasNoErrors();

        $this->assertSame(0, Booking::firstOrFail()->single_rooms);
    }

    // Helpers

    private function setFees(): void
    {
        Setting::set(SettingKey::BookingFee, '25.00');
        Setting::set(SettingKey::GuaranteeFund, '10.00');
        Setting::set(SettingKey::EmergencyFund, '2.50');
    }

    private function occupy(int $travelers, Status $status = Status::New): Booking
    {
        return Booking::factory()->onDeparture($this->departure, adults: $travelers)->create(['status' => $status]);
    }

    private function createBooking(int $adults = 2, array $overrides = []): Booking
    {
        $this->post(route('admin.bookings.store'), $this->payload(adults: $adults, overrides: $overrides))->assertSessionHasNoErrors();

        return Booking::latest('id')->firstOrFail();
    }

    private function payload(int $adults = 2, int $children = 0, array $overrides = []): array
    {
        return array_merge([
            'trip' => ['id' => $this->trip->id],
            'departure_id' => $this->departure->id,
            'has_accepted_conditions' => true,
            'has_confirmed' => true,
            'travelers' => [
                'adults' => $this->travelers($adults, TravelerType::Adult),
                'children' => $this->travelers($children, TravelerType::Child),
            ],
            'main_booker' => 0,
            'contact' => BookingContact::factory()->make()->toArray(),
            'cost_items' => [
                ['category' => CostCategory::Train->value, 'label' => 'Trein', 'amount_per_person' => 40000, 'quantity' => 2],
            ],
            'margin_percentage' => 15,
        ], $overrides);
    }

    private function travelers(int $count, TravelerType $type): array
    {
        return BookingTraveler::factory()->count($count)->{$type->value}()->make()
            ->map(fn (BookingTraveler $traveler) => [
                ...$traveler->only(['first_name', 'last_name', 'nationality']),
                'full_name' => $traveler->full_name,
                'birthdate' => Carbon::parse($traveler->birthdate)->format('d-m-Y'),
            ])
            ->all();
    }

    private function updatePayload(Booking $booking, array $overrides = []): array
    {
        $booking->load('travelers', 'contact');

        $travelers = fn (TravelerType $type) => $booking->travelers->where('type', $type)->map(fn (BookingTraveler $traveler) => [
            'id' => $traveler->id,
            'first_name' => $traveler->first_name,
            'last_name' => $traveler->last_name,
            'birthdate' => $traveler->birthdate->format('d-m-Y'),
            'nationality' => $traveler->nationality,
        ])->values()->all();

        return array_merge([
            'status' => $booking->status->value,
            'payment_status' => PaymentStatus::Pending->value,
            'return_date' => $booking->return_date->toDateString(),
            'travelers' => [
                'adults' => $travelers(TravelerType::Adult),
                'children' => $travelers(TravelerType::Child),
            ],
            'main_booker' => 0,
            'contact' => $booking->contact->only(['street', 'house_number', 'addition', 'postal_code', 'city', 'email', 'phone']),
            'cost_items' => [
                ['category' => CostCategory::Train->value, 'label' => 'Trein', 'amount_per_person' => 40000, 'quantity' => 2],
            ],
            'margin_percentage' => 15,
        ], $overrides);
    }
}
