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
        $date = now()->addMonth(3);

        $payload = [
            'trip' => [
                'id' => $trip->id,
            ],
            'conditions' => true,
            'confirmed' => true,
            'departure_date' => $date,
            'travelers' => [
                'adults' => [
                    [
                        'first_name' => 'John',
                        'last_name' => 'Doe',
                        'full_name' => 'John Doe',
                        'birthdate' => now()->subYears(35)->format('d-m-Y'),
                        'nationality' => 'NL',
                    ],
                    [
                        'first_name' => 'Jane',
                        'last_name' => 'Doe',
                        'full_name' => 'Jane Doe',
                        'birthdate' => now()->subYears(45)->format('d-m-Y'),
                        'nationality' => 'NL',
                    ],
                ],
                'children' => [
                    [
                        'first_name' => 'Junior',
                        'last_name' => 'Doe',
                        'full_name' => 'Junior Doe',
                        'birthdate' => now()->subYears(8)->format('d-m-Y'),
                        'nationality' => 'NL',
                    ],
                ],
            ],
            'main_booker' => 0,
            'contact' => [
                'street' => 'Teststraat',
                'house_number' => '123',
                'addition' => 'bis',
                'postal_code' => '1234AB',
                'city' => 'Teststad',
                'email' => 'test@test.nl',
                'phone' => '+31612345678',
            ],
        ];

        $response = $this->post(route('bookings.store'), $payload);

        $booking = Booking::firstOrFail();
        $this->assertEquals($payload['trip']['id'], $booking->product_id);
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'departure_date' => $date->format('Y-m-d'),
            'confirmed' => 1,
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
