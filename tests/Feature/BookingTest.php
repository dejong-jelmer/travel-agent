<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\BookingTraveler;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_create_a_booking_with_travelers_and_contact()
    {
        $trip = Product::factory()->create();
        $date = fake()->dateTimeBetween('now', '+1 year')->format('Y-m-d');
        $adults = [];
        $children = [];
        $numberOfAdults = fake()->numberBetween(1, 3);
        $numberOfChildren = fake()->numberBetween(0, 3);

        for ($i = 0; $i <= $numberOfAdults; $i++) {
            $firstName = fake()->firstName();
            $lastName = fake()->lastName();
            $adults[$i] = [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'full_name' => "{$firstName} {$lastName}",
                'birthdate' => fake()->dateTimeBetween('now -80 year', 'now -12 year')->format('d-m-Y'),
                'nationality' => fake()->country(),
            ];
        }

        for ($i = 0; $i <= $numberOfChildren; $i++) {
            $firstName = fake()->firstName();
            $lastName = fake()->lastName();
            $children[$i] = [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'full_name' => "{$firstName} {$lastName}",
                'birthdate' => fake()->dateTimeBetween('now -12 year', 'now')->format('d-m-Y'),
                'nationality' => fake()->country(),
            ];
        }

        $payload = [
            'trip' => [
                'id' => $trip->id,
            ],
            'conditions_accepted' => true,
            'is_confirmed' => true,
            'departure_date' => $date,
            'travelers' => [
                'adults' => [
                    ...$adults,
                ],
                'children' => [
                    ...$children,
                ],
            ],
            'main_booker' => 0,
            'contact' => [
                'street' => fake()->streetname(),
                'house_number' => fake()->randomNumber(3, false),
                'addition' => fake()->streetSuffix(),
                'postal_code' => fake()->postcode(),
                'city' => fake()->city(),
                'email' => fake()->email(),
                'phone' => fake()->phoneNumber(),
            ],
        ];

        $response = $this->post(route('bookings.store'), $payload);
        // if ($response->isRedirect()) {
        //     dd($response->getTargetUrl());
        // }
        // dd($response->getContent());
        $booking = Booking::firstOrFail();
        $this->assertEquals($payload['trip']['id'], $booking->product_id);
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'departure_date' => $date,
            'is_confirmed' => 1,
        ]);

        // UUID check
        $this->assertNotNull($booking->uuid);
        $this->assertTrue(Str::isUuid($booking->uuid));

        // Reference check
        $this->assertNotNull($booking->reference);
        $this->assertMatchesRegularExpression(
            "/^\d{4}-\d{6}$/",
            $booking->reference
        );

        // Check if unique
        $otherBooking = Booking::factory()
            ->for(Product::factory(), 'product')
            ->create();
        $this->assertNotEquals($booking->reference, $otherBooking->reference);

        // Check travelers
        foreach (Arr::flatten($payload['travelers'], 1) as $traveler) {
            $this->assertDatabaseHas('booking_travelers', [
                'booking_id' => $booking->id,
                'first_name' => $traveler['first_name'],
                'last_name' => $traveler['last_name'],
                'birthdate' => Carbon::parse($traveler['birthdate'])->format('Y-m-d'),
                'nationality' => $traveler['nationality'],
            ]);
        }

        // Check contact details
        $this->assertDatabaseHas('booking_contacts', [
            'booking_id' => $booking->id,
            ...$payload['contact'],
        ]);

        // Check redirect
        $response->assertRedirect(route('bookings.confirmation', ['booking' => $booking->uuid]));
    }

    public function test_booking_has_main_booker_relation()
    {
        $booking = Booking::factory()
            ->for(Product::factory(), 'product')
            ->create();
        $traveler = BookingTraveler::factory()->create([
            'booking_id' => $booking->id,
        ]);
        $booking->update([
            'main_booker_id' => $traveler->id,
        ]);

        $this->assertTrue($booking->mainBooker->is($traveler));
    }
}
