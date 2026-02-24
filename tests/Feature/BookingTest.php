<?php

namespace Tests\Feature;

use App\Enums\Booking\PaymentStatus;
use App\Enums\Booking\Status;
use App\Enums\SettingKey;
use App\Enums\TravelerType;
use App\Models\Booking;
use App\Models\BookingTraveler;
use App\Models\Setting;
use App\Models\Trip;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Tests\TestCase;

use function PHPSTORM_META\override;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    private Trip $trip;

    protected function setUp(): void
    {
        parent::setUp();
        $this->trip = Trip::factory()->create();
    }

    public function test_it_can_create_a_booking_with_travelers_and_contact()
    {
        $payload = $this->generateBookingPayload();

        $response = $this->post(route('bookings.store'), $payload);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $booking = Booking::firstOrFail();

        $this->assertBookingWasCreatedCorrectly($booking, $payload);
        $this->assertTravelersWereCreatedCorrectly($booking, $payload);
        $this->assertContactWasCreatedCorrectly($booking, $payload);
        $this->assertRedirectIsCorrect($response, $booking);
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

        $response = $this->post(route('bookings.store'), $payload);

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

        $response = $this->post(route('bookings.store'), $payload);

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

        $response = $this->post(route('bookings.store'), $payload);

        $response->assertSessionHasErrors('departure_date');
    }

    public function test_booking_is_accepted_when_departure_date_does_not_match_any_blocked_date(): void
    {
        $trip = Trip::factory()->create([
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

        $response = $this->post(route('bookings.store'), $payload);

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

        $response = $this->post(route('bookings.store'), $payload);

        $response->assertSessionHasErrors('departure_date');
    }

    public function test_booking_is_accepted_when_departure_date_is_on_season_end(): void
    {
        $seasonEnd = now()->addMonths(2);
        Setting::set(SettingKey::BookingSeasonEnd, $seasonEnd->format('Y-m-d'));

        $payload = $this->generateBookingPayload([
            'departure_date' => $seasonEnd->format('Y-m-d'),
        ]);

        $response = $this->post(route('bookings.store'), $payload);

        $response->assertSessionHasNoErrors();
    }

    public function test_booking_is_accepted_when_departure_date_is_before_season_end(): void
    {
        Setting::set(SettingKey::BookingSeasonEnd, now()->addMonths(3)->format('Y-m-d'));

        $payload = $this->generateBookingPayload([
            'departure_date' => now()->addMonth()->format('Y-m-d'),
        ]);

        $response = $this->post(route('bookings.store'), $payload);

        $response->assertSessionHasNoErrors();
    }

    public function test_booking_is_accepted_when_no_season_end_is_set(): void
    {
        $payload = $this->generateBookingPayload([
            'departure_date' => now()->addYear()->format('Y-m-d'),
        ]);

        $response = $this->post(route('bookings.store'), $payload);

        $response->assertSessionHasNoErrors();
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
                    ],
                ],
                'children' => [
                    [
                        'first_name' => fake()->firstName(),
                        'last_name' => fake()->lastName(),
                        'birthdate' => $this->generateBirthdate(TravelerType::Child),
                    ],
                ],
            ],
        ];
    }

    private function generateBookingPayload(array $overrides = []): array
    {
        $numberOfAdults = $overrides['numberOfAdults'] ?? fake()->numberBetween(1, 4);
        $numberOfChildren = $overrides['numberOfChildren'] ?? fake()->numberBetween(0, 2);

        return array_merge([
            'trip' => ['id' => $this->trip->id],
            'has_accepted_conditions' => true,
            'has_confirmed' => true,
            'departure_date' => fake()->dateTimeBetween('now', '+1 year')->format('Y-m-d'),
            'travelers' => [
                'adults' => $this->generateTravelers($numberOfAdults, TravelerType::Adult),
                'children' => $this->generateTravelers($numberOfChildren, TravelerType::Child),
            ],
            'main_booker' => 0,
            'contact' => $this->generateContactData(),
        ], $overrides);
    }

    private function generateTravelers(int $count, TravelerType $type = TravelerType::Adult): array
    {
        $travelers = [];

        for ($i = 0; $i < $count; $i++) {
            $firstName = fake()->firstName();
            $lastName = fake()->lastName();
            $travelers[] = [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'full_name' => "{$firstName} {$lastName}",
                'birthdate' => $this->generateBirthdate($type),
                'nationality' => fake()->country(),
            ];
        }

        return $travelers;
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
        return [
            'street' => fake()->streetName(),
            'house_number' => (string) fake()->numberBetween(1, 999),
            'addition' => fake()->optional()->bothify('?#'),
            'postal_code' => fake()->postcode(),
            'city' => fake()->city(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
        ];
    }

    private function createBookingWithTravelersAndContact(): Booking
    {
        $payload = $this->generateBookingPayload();
        $this->post(route('bookings.store'), $payload);

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

    private function assertRedirectIsCorrect($response, Booking $booking): void
    {
        $response->assertRedirect(route('bookings.received', ['booking' => $booking->uuid]));
    }

    private function generateUpdatePayload(Booking $booking, array $overrides = []): array
    {
        $booking->load('travelers');

        $adults = [];
        $children = [];

        foreach ($booking->travelers as $traveler) {
            $travelerData = [
                'id' => $traveler->id,
                'first_name' => $traveler->first_name,
                'last_name' => $traveler->last_name,
                'birthdate' => $traveler->birthdate->format('d-m-Y'),
                'nationality' => $traveler->nationality,
            ];

            match ($traveler->type) {
                TravelerType::Adult => $adults[] = $travelerData,
                TravelerType::Child => $children[] = $travelerData,
            };
        }

        // Handle traveler overrides safely by limiting to actual traveler count
        if (isset($overrides['travelers']['adults'])) {
            $adultOverrides = $overrides['travelers']['adults'];
            $adultCount = count($adults);

            // Limit override array to actual count
            if (count($adultOverrides) > $adultCount) {
                $overrides['travelers']['adults'] = array_slice($adultOverrides, 0, $adultCount);
            }
        }

        if (isset($overrides['travelers']['children'])) {
            $childrenOverrides = $overrides['travelers']['children'];
            $childrenCount = count($children);

            // Limit override array to actual count
            if (count($childrenOverrides) > $childrenCount) {
                $overrides['travelers']['children'] = array_slice($childrenOverrides, 0, $childrenCount);
            }
        }

        // Create the base payload
        $payload = [
            'trip' => ['id' => $booking->trip_id],
            'status' => Status::New->value,
            'payment_status' => PaymentStatus::Pending->value,
            'travelers' => [
                'adults' => $adults,
                'children' => $children,
            ],
            'main_booker' => $booking->main_booker_id ?? 0,
            'contact' => [
                'street' => $booking->contact->street,
                'house_number' => $booking->contact->house_number,
                'addition' => $booking->contact->addition,
                'postal_code' => $booking->contact->postal_code,
                'city' => $booking->contact->city,
                'email' => $booking->contact->email,
                'phone' => $booking->contact->phone,
            ],
        ];

        // Apply overrides recursively
        return array_replace_recursive($payload, $overrides);
    }
}
