<?php

namespace Tests\Feature;

use App\Enums\Booking\CostCategory;
use App\Enums\Booking\PaymentStatus;
use App\Enums\Booking\Status;
use App\Enums\SettingKey;
use App\Enums\TravelerType;
use App\Events\BookingCreated;
use App\Mail\AdminBookingNotificationMail;
use App\Mail\BookingConfirmationMail;
use App\Models\Booking;
use App\Models\BookingContact;
use App\Models\BookingTraveler;
use App\Models\Setting;
use App\Models\Trip;
use App\Models\User;
use App\Services\TermsPdfService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    private Trip $trip;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->actingAs($this->admin);

        $this->trip = Trip::factory()->withPrices()->create();
    }

    public function test_admin_can_create_a_booking_with_travelers_and_contact()
    {
        $payload = $this->generateBookingPayload();
        $response = $this->post(route('admin.bookings.store'), $payload);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $booking = Booking::firstOrFail();

        $this->assertBookingWasCreatedCorrectly($booking, $payload);
        $this->assertTravelersWereCreatedCorrectly($booking, $payload);
        $this->assertContactWasCreatedCorrectly($booking, $payload);
        $this->assertPricesWhereSetCorrectly($booking, $payload);
    }

    public function test_booking_persists_cost_items_and_calculated_price_with_vezere_payload(): void
    {
        $payload = $this->generateBookingPayload([
            'numberOfAdults' => 2,
            'numberOfChildren' => 0,
            'cost_items' => [
                ['category' => CostCategory::Train->value, 'label' => 'TGV Parijs–Bordeaux retour', 'amount_per_person' => 40000, 'quantity' => 2],
                ['category' => CostCategory::Accommodation->value, 'label' => 'Boutiquehotel Sarlat', 'amount_per_person' => 33000, 'quantity' => 2],
                ['category' => CostCategory::Transfer->value, 'label' => 'Transfer station-hotel', 'amount_per_person' => 9000, 'quantity' => 2],
                ['category' => CostCategory::Ticket->value, 'label' => 'Toegang grottenroute', 'amount_per_person' => 3000, 'quantity' => 2],
            ],
            'margin_percentage' => 35,
        ]);

        $response = $this->post(route('admin.bookings.store'), $payload);
        $response->assertSessionHasNoErrors();

        $booking = Booking::firstOrFail()->load('costItems');

        $this->assertCount(4, $booking->costItems);
        $this->assertSame(170000, $booking->total_cost);
        $this->assertSame(3500, $booking->margin_basis_points);
        // 170000 / (1 - 0.35) = 261538.461... rounded to 261538.
        $this->assertSame(261538, $booking->calculated_price);
        $this->assertSame(261538, $booking->display_price);
    }

    public function test_final_price_overrides_calculated_price(): void
    {
        $payload = $this->generateBookingPayload([
            'numberOfAdults' => 2,
            'numberOfChildren' => 0,
            'final_price' => 260000,
        ]);

        $response = $this->post(route('admin.bookings.store'), $payload);
        $response->assertSessionHasNoErrors();

        $booking = Booking::firstOrFail();

        $this->assertSame(260000, $booking->final_price);
        $this->assertSame(260000, $booking->display_price);
    }

    public function test_booking_creation_rejects_empty_cost_items(): void
    {
        $payload = $this->generateBookingPayload(['cost_items' => []]);

        $response = $this->post(route('admin.bookings.store'), $payload);

        $response->assertSessionHasErrors('cost_items');
    }

    public function test_admin_can_update_the_booking_travelers_and_contact_details()
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        $booking = $this->createBookingWithTravelersAndContact();
        $overrides = $this->getUpdateOverrides();

        $updatedPayload = $this->generateUpdatePayload($booking, $overrides);

        $response = $this->put(route('admin.bookings.update', $booking), $updatedPayload);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $booking->refresh();

        $this->assertTravelersWereUpdatedCorrectly($booking, $updatedPayload);
        $this->assertContactWasUpdatedCorrectly($booking, $updatedPayload);
    }

    public function test_booking_has_unique_reference()
    {
        $booking1 = Booking::factory()->for($this->trip, 'trip')->create();
        $booking2 = Booking::factory()->for($this->trip, 'trip')->create();

        $this->assertNotNull($booking1->reference);
        $this->assertNotNull($booking2->reference);
        $this->assertNotEquals($booking1->reference, $booking2->reference);
        $this->assertMatchesRegularExpression("/^\d{4}-\d{6}$/", $booking1->reference);
    }

    public function test_booking_has_valid_uuid()
    {
        $booking = Booking::factory()->for($this->trip, 'trip')->create();

        $this->assertNotNull($booking->uuid);
        $this->assertTrue(Str::isUuid($booking->uuid));
    }

    public function test_booking_has_main_booker_relation()
    {
        $booking = Booking::factory()->for($this->trip, 'trip')->create();
        $traveler = BookingTraveler::factory()->create(['booking_id' => $booking->id]);
        $booking->update(['main_booker_id' => $traveler->id]);

        $this->assertTrue($booking->mainBooker->is($traveler));
    }

    // Blocked dates backend enforcement tests
    public function test_booking_is_rejected_when_departure_date_is_a_blocked_weekday(): void
    {
        $nextMonday = now()->next(Carbon::MONDAY);

        $trip = Trip::factory()->create([
            'blocked_dates' => ['dates' => [], 'weekdays' => [Carbon::MONDAY]],
        ]);

        $payload = $this->generateBookingPayload([
            'trip' => ['id' => $trip->id],
            'departure_date' => $nextMonday->format('Y-m-d'),
        ]);

        $response = $this->post(route('admin.bookings.store'), $payload);

        $response->assertSessionHasErrors('departure_date');
    }

    public function test_booking_is_rejected_when_departure_date_is_a_blocked_specific_date(): void
    {
        $blockedDate = now()->addMonth();

        $trip = Trip::factory()->create([
            'blocked_dates' => ['dates' => [$blockedDate->format('Y-m-d')], 'weekdays' => []],
        ]);

        $payload = $this->generateBookingPayload([
            'trip' => ['id' => $trip->id],
            'departure_date' => $blockedDate->format('Y-m-d'),
        ]);

        $response = $this->post(route('admin.bookings.store'), $payload);

        $response->assertSessionHasErrors('departure_date');
    }

    public function test_booking_is_rejected_when_departure_date_falls_within_a_blocked_range(): void
    {
        $rangeStart = now()->addMonth();
        $rangeEnd = now()->addMonths(2);
        $dateInRange = now()->addMonth()->addDays(7);

        $trip = Trip::factory()->create([
            'blocked_dates' => [
                'dates' => [['start' => $rangeStart->format('Y-m-d'), 'end' => $rangeEnd->format('Y-m-d')]],
                'weekdays' => [],
            ],
        ]);

        $payload = $this->generateBookingPayload([
            'trip' => ['id' => $trip->id],
            'departure_date' => $dateInRange->format('Y-m-d'),
        ]);

        $response = $this->post(route('admin.bookings.store'), $payload);

        $response->assertSessionHasErrors('departure_date');
    }

    public function test_booking_is_accepted_when_departure_date_does_not_match_any_blocked_date(): void
    {
        $trip = Trip::factory()->withPrices()->create([
            'blocked_dates' => [
                'dates' => [now()->addMonths(3)->format('Y-m-d')],
                'weekdays' => [Carbon::SUNDAY],
            ],
        ]);

        $availableDate = now()->next(Carbon::MONDAY)->addMonths(4);

        $payload = $this->generateBookingPayload([
            'trip' => ['id' => $trip->id],
            'departure_date' => $availableDate->format('Y-m-d'),
        ]);

        $response = $this->post(route('admin.bookings.store'), $payload);

        $response->assertSessionHasNoErrors();
    }

    // Booking season end constraint tests

    public function test_booking_is_rejected_when_departure_date_exceeds_season_end(): void
    {
        $seasonEnd = now()->addMonths(2);
        Setting::set(SettingKey::BookingSeasonEnd, $seasonEnd->format('Y-m-d'));

        $payload = $this->generateBookingPayload([
            'departure_date' => $seasonEnd->addDay()->format('Y-m-d'),
        ]);

        $response = $this->post(route('admin.bookings.store'), $payload);

        $response->assertSessionHasErrors('departure_date');
    }

    public function test_booking_is_accepted_when_departure_date_is_on_season_end(): void
    {
        $seasonEnd = now()->addMonths(2);
        Setting::set(SettingKey::BookingSeasonEnd, $seasonEnd->format('Y-m-d'));

        $payload = $this->generateBookingPayload([
            'departure_date' => $seasonEnd->format('Y-m-d'),
        ]);

        $response = $this->post(route('admin.bookings.store'), $payload);

        $response->assertSessionHasNoErrors();
    }

    public function test_booking_is_accepted_when_departure_date_is_before_season_end(): void
    {
        Setting::set(SettingKey::BookingSeasonEnd, now()->addMonths(3)->format('Y-m-d'));

        $payload = $this->generateBookingPayload([
            'departure_date' => now()->addMonth()->format('Y-m-d'),
        ]);

        $response = $this->post(route('admin.bookings.store'), $payload);

        $response->assertSessionHasNoErrors();
    }

    // Event & Mail tests

    public function test_booking_creation_dispatches_booking_created_event(): void
    {
        Event::fake([BookingCreated::class]);

        $payload = $this->generateBookingPayload();

        $this->post(route('admin.bookings.store'), $payload);

        Event::assertDispatched(BookingCreated::class, function ($event) {
            return $event->booking instanceof Booking;
        });
    }

    public function test_booking_created_event_queues_confirmation_email(): void
    {
        Mail::fake();

        $payload = $this->generateBookingPayload();

        $this->post(route('admin.bookings.store'), $payload);

        Mail::assertSent(BookingConfirmationMail::class, function ($mail) use ($payload) {
            return $mail->hasTo($payload['contact']['email']);
        });
    }

    public function test_booking_created_event_queues_admin_notification_email(): void
    {
        Mail::fake();

        $payload = $this->generateBookingPayload();

        $this->post(route('admin.bookings.store'), $payload);

        $adminAddress = config('booking.mail');

        Mail::assertSent(AdminBookingNotificationMail::class, function ($mail) use ($adminAddress) {
            return $mail->hasTo($adminAddress);
        });
    }

    public function test_booking_confirmation_mail_has_terms_pdf_attachment(): void
    {
        $booking = Booking::factory()->for($this->trip, 'trip')->withTravelers(adults: 1)->create();

        $mail = new BookingConfirmationMail($booking);
        $attachments = $mail->attachments();

        $this->assertCount(1, $attachments);
        $this->assertSame(TermsPdfService::FILENAME, $attachments[0]->as);
    }

    // Helper Methods
    private function getUpdateOverrides(): array
    {
        return [
            'contact' => [
                'street' => fake()->streetName(),
                'email' => fake()->safeEmail(),
                'postal_code' => fake()->postcode(),
            ],
            'travelers' => [
                'adults' => [
                    [
                        'first_name' => fake()->firstName(),
                        'last_name' => fake()->lastName(),
                        'birthdate' => $this->generateBirthdate(TravelerType::Adult),
                        'nationality' => fake()->country(),
                    ],
                ],
                'children' => [
                    [
                        'first_name' => fake()->firstName(),
                        'last_name' => fake()->lastName(),
                        'birthdate' => $this->generateBirthdate(TravelerType::Child),
                        'nationality' => fake()->country(),
                    ],
                ],
            ],
        ];
    }

    private function generateBookingPayload(array $overrides = []): array
    {
        $numberOfAdults = $overrides['numberOfAdults'] ?? fake()->numberBetween(1, 4);
        $numberOfChildren = $overrides['numberOfChildren'] ?? fake()->numberBetween(0, 2);
        $departureDate = fake()->dateTimeBetween('now', '+1 year');

        return array_merge([
            'trip' => ['id' => $this->trip->id],
            'has_accepted_conditions' => true,
            'has_confirmed' => true,
            'departure_date' => $departureDate->format('Y-m-d'),
            'return_date' => Carbon::instance($departureDate)->addDays(7),
            'travelers' => [
                'adults' => $this->generateTravelers($numberOfAdults, TravelerType::Adult),
                'children' => $this->generateTravelers($numberOfChildren, TravelerType::Child),
            ],
            'main_booker' => 0,
            'contact' => $this->generateContactData(),
            'cost_items' => $this->defaultCostItems(),
            'margin_percentage' => 35,
        ], $overrides);
    }

    private function defaultCostItems(): array
    {
        return [
            ['category' => CostCategory::Train->value, 'label' => 'Trein heen en terug', 'amount_per_person' => 40000, 'quantity' => 2],
            ['category' => CostCategory::Accommodation->value, 'label' => 'Hotel', 'amount_per_person' => 30000, 'quantity' => 2],
        ];
    }

    private function generateTravelers(int $count, TravelerType $type): array
    {
        return BookingTraveler::factory()
            ->count($count)
            ->{$type->value}()
            ->make()
            ->map(fn ($t) => [
                ...$t->only(['first_name', 'last_name', 'nationality']),
                'full_name' => $t->full_name,
                'birthdate' => Carbon::parse($t->birthdate)->format('d-m-Y'),
            ])
            ->all();
    }

    private function generateBirthdate(TravelerType $type): string
    {
        return match ($type) {
            TravelerType::Adult => fake()->dateTimeBetween('-80 years', '-18 years')->format('d-m-Y'),
            TravelerType::Child => fake()->dateTimeBetween('-12 years', 'now')->format('d-m-Y'),
            default => fake()->dateTimeBetween('-80 years', 'now')->format('d-m-Y'),
        };
    }

    private function generateContactData(): array
    {
        return BookingContact::factory()->make()->toArray();
    }

    private function createBookingWithTravelersAndContact(): Booking
    {
        $payload = $this->generateBookingPayload([
            'numberOfAdults' => 2,
            'numberOfChildren' => 1,
        ]);
        $this->post(route('admin.bookings.store'), $payload);

        return Booking::firstOrFail();
    }

    // Assertion Methods

    private function assertBookingWasCreatedCorrectly(Booking $booking, array $payload): void
    {
        $this->assertEquals($payload['trip']['id'], $booking->trip_id);
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'departure_date' => $payload['departure_date'],
            'has_confirmed' => 1,
            'has_accepted_conditions' => 1,
        ]);
    }

    private function assertTravelersWereCreatedCorrectly(Booking $booking, array $payload): void
    {
        $allTravelers = Arr::flatten($payload['travelers'], 1);

        foreach ($allTravelers as $traveler) {
            $this->assertDatabaseHas('booking_travelers', [
                'booking_id' => $booking->id,
                'first_name' => $traveler['first_name'],
                'last_name' => $traveler['last_name'],
                'birthdate' => Carbon::createFromFormat('d-m-Y', $traveler['birthdate'])->format('Y-m-d'),
                'nationality' => $traveler['nationality'],
            ]);
        }

        $this->assertCount(count($allTravelers), $booking->travelers);
    }

    private function assertTravelersWereUpdatedCorrectly(Booking $booking, array $payload): void
    {
        $this->assertTravelersWereCreatedCorrectly($booking, $payload);
    }

    private function assertContactWasCreatedCorrectly(Booking $booking, array $payload): void
    {
        $this->assertDatabaseHas('booking_contacts', [
            'booking_id' => $booking->id,
            'street' => $payload['contact']['street'],
            'house_number' => $payload['contact']['house_number'],
            'postal_code' => $payload['contact']['postal_code'],
            'city' => $payload['contact']['city'],
            'email' => $payload['contact']['email'],
            'phone' => $payload['contact']['phone'],
        ]);
    }

    private function assertContactWasUpdatedCorrectly(Booking $booking, array $payload): void
    {
        $this->assertContactWasCreatedCorrectly($booking, $payload);
    }

    private function assertPricesWhereSetCorrectly(Booking $booking, array $payload): void
    {
        $booking->load('costItems');

        $this->assertCount(count($payload['cost_items']), $booking->costItems);

        $expectedTotalCost = collect($payload['cost_items'])
            ->sum(fn (array $item) => $item['amount_per_person'] * $item['quantity']);

        $this->assertSame($expectedTotalCost, $booking->total_cost);
        $this->assertSame(
            (int) round((float) $payload['margin_percentage'] * 100),
            $booking->margin_basis_points,
        );

        $margin = $booking->margin_basis_points / 10000;
        $expectedCalculated = (int) round($expectedTotalCost / (1 - $margin));

        $this->assertSame($expectedCalculated, $booking->calculated_price);
    }

    private function generateUpdatePayload(Booking $booking, array $overrides = []): array
    {
        $booking->load('travelers', 'contact');

        $travelers = $booking->travelers->groupBy(fn ($t) => $t->type->value)
            ->map(fn ($group) => $group->map(fn ($t) => [
                'id' => $t->id,
                'first_name' => $t->first_name,
                'last_name' => $t->last_name,
                'birthdate' => $t->birthdate->format('d-m-Y'),
                'nationality' => $t->nationality,
            ])->values()->all())
            ->all();

        $payload = [
            'trip' => ['id' => $booking->trip_id],
            'status' => Status::New->value,
            'payment_status' => PaymentStatus::Pending->value,
            'return_date' => $booking->departure_date->addDays(7),
            'travelers' => [
                'adults' => $travelers['adult'] ?? [],
                'children' => $travelers['child'] ?? [],
            ],
            'main_booker' => $booking->main_booker_id ?? 0,
            'contact' => $booking->contact->only([
                'street',
                'house_number',
                'addition',
                'postal_code',
                'city',
                'email',
                'phone',
            ]),
            'cost_items' => $this->defaultCostItems(),
            'margin_percentage' => 35,
        ];

        return array_replace_recursive($payload, $overrides);
    }
}
