<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\BookingChange;
use App\Models\BookingTraveler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnonymizeSpecialRequestsTest extends TestCase
{
    use RefreshDatabase;

    private function createBookingWithSpecialRequests(array $bookingOverrides = [], array $travelerOverrides = []): Booking
    {
        $booking = Booking::factory()
            ->withTravelers(1, 0)
            ->create($bookingOverrides);

        $booking->travelers()->update(array_merge([
            'special_requests' => 'Vegetarian meals please',
            'special_requests_consent' => true,
            'special_requests_consent_at' => now(),
        ], $travelerOverrides));

        return $booking->fresh();
    }

    public function test_it_anonymizes_special_requests_after_return_date_plus_grace_period(): void
    {
        $booking = $this->createBookingWithSpecialRequests([
            'return_date' => now()->subDays(8),
        ]);

        $this->artisan('bookings:anonymize-special-requests')
            ->assertExitCode(0);

        $traveler = $booking->travelers->first()->fresh();
        $this->assertNull($traveler->special_requests);
        $this->assertNotNull($traveler->special_requests_anonymized_at);
    }

    public function test_it_preserves_consent_fields_after_anonymization(): void
    {
        $booking = $this->createBookingWithSpecialRequests([
            'return_date' => now()->subDays(8),
        ]);

        $this->artisan('bookings:anonymize-special-requests')
            ->assertExitCode(0);

        $traveler = $booking->travelers->first()->fresh();
        $this->assertTrue($traveler->special_requests_consent);
        $this->assertNotNull($traveler->special_requests_consent_at);
    }

    public function test_it_does_not_anonymize_within_grace_period(): void
    {
        $booking = $this->createBookingWithSpecialRequests([
            'return_date' => now()->subDays(5),
        ]);

        $this->artisan('bookings:anonymize-special-requests')
            ->assertExitCode(0);

        $traveler = $booking->travelers->first()->fresh();
        $this->assertNotNull($traveler->special_requests);
        $this->assertNull($traveler->special_requests_anonymized_at);
    }

    public function test_it_does_not_anonymize_future_bookings(): void
    {
        $booking = $this->createBookingWithSpecialRequests([
            'return_date' => now()->addDays(10),
        ]);

        $this->artisan('bookings:anonymize-special-requests')
            ->assertExitCode(0);

        $traveler = $booking->travelers->first()->fresh();
        $this->assertNotNull($traveler->special_requests);
    }

    public function test_it_skips_bookings_where_special_requests_are_already_null(): void
    {
        $this->createBookingWithSpecialRequests([
            'return_date' => now()->subDays(8),
        ], [
            'special_requests' => null,
        ]);

        $this->artisan('bookings:anonymize-special-requests')
            ->expectsOutput('No bookings found with special requests to anonymize.')
            ->assertExitCode(0);
    }

    public function test_dry_run_does_not_modify_data(): void
    {
        $booking = $this->createBookingWithSpecialRequests([
            'return_date' => now()->subDays(8),
        ]);

        $this->artisan('bookings:anonymize-special-requests', ['--dry-run' => true])
            ->assertExitCode(0);

        $traveler = $booking->travelers->first()->fresh();
        $this->assertNotNull($traveler->special_requests);
        $this->assertNull($traveler->special_requests_anonymized_at);
    }

    public function test_custom_days_option_works(): void
    {
        $booking = $this->createBookingWithSpecialRequests([
            'return_date' => now()->subDays(2),
        ]);

        $this->artisan('bookings:anonymize-special-requests', ['--days' => 1])
            ->assertExitCode(0);

        $traveler = $booking->travelers->first()->fresh();
        $this->assertNull($traveler->special_requests);
        $this->assertNotNull($traveler->special_requests_anonymized_at);
    }

    public function test_it_deletes_booking_change_records_for_special_requests(): void
    {
        $booking = $this->createBookingWithSpecialRequests([
            'return_date' => now()->subDays(8),
        ]);

        $traveler = $booking->travelers->first();

        // Create audit records
        BookingChange::create([
            'booking_id' => $booking->id,
            'model_type' => BookingTraveler::class,
            'model_id' => $traveler->id,
            'field' => 'special_requests',
            'old_value' => null,
            'new_value' => 'Vegetarian meals please',
        ]);

        BookingChange::create([
            'booking_id' => $booking->id,
            'model_type' => BookingTraveler::class,
            'model_id' => $traveler->id,
            'field' => 'first_name',
            'old_value' => 'Old Name',
            'new_value' => 'New Name',
        ]);

        $this->artisan('bookings:anonymize-special-requests')
            ->assertExitCode(0);

        $this->assertDatabaseMissing('booking_changes', [
            'booking_id' => $booking->id,
            'field' => 'special_requests',
        ]);

        // Other change records should be preserved
        $this->assertDatabaseHas('booking_changes', [
            'booking_id' => $booking->id,
            'field' => 'first_name',
        ]);
    }

    public function test_it_preserves_booking_change_records_for_consent_fields(): void
    {
        $booking = $this->createBookingWithSpecialRequests([
            'return_date' => now()->subDays(8),
        ]);

        $traveler = $booking->travelers->first();

        BookingChange::create([
            'booking_id' => $booking->id,
            'model_type' => BookingTraveler::class,
            'model_id' => $traveler->id,
            'field' => 'special_requests_consent',
            'old_value' => false,
            'new_value' => true,
        ]);

        $this->artisan('bookings:anonymize-special-requests')
            ->assertExitCode(0);

        $this->assertDatabaseHas('booking_changes', [
            'booking_id' => $booking->id,
            'field' => 'special_requests_consent',
        ]);
    }

    public function test_it_skips_fully_anonymized_bookings(): void
    {
        $booking = $this->createBookingWithSpecialRequests([
            'return_date' => now()->subDays(8),
            'anonymized_at' => now(),
        ]);

        $this->artisan('bookings:anonymize-special-requests')
            ->expectsOutput('No bookings found with special requests to anonymize.')
            ->assertExitCode(0);

        $traveler = $booking->travelers->first()->fresh();
        $this->assertNotNull($traveler->special_requests);
    }
}
