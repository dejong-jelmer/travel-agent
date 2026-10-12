<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Trip;
use App\Services\TravelBookingDetails;
use App\Travel\Contracts\BookingDetails;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TravelBookingDetailsTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_app_gives_the_travel_module_its_booking_details(): void
    {
        $this->assertInstanceOf(TravelBookingDetails::class, app(BookingDetails::class));
    }

    public function test_the_title_is_the_name_of_the_trip(): void
    {
        $booking = Booking::factory()->for(Trip::factory()->state(['name' => 'De Vézère-vallei & Bordeaux']))->create();

        $this->assertSame('De Vézère-vallei & Bordeaux', app(BookingDetails::class)->title($booking));
    }

    public function test_the_travelers_are_named_in_full_the_main_booker_first(): void
    {
        $booking = Booking::factory()->withTravelers(2, 1)->create();
        $mainBooker = $booking->mainBooker;

        $names = app(BookingDetails::class)->travelerNames($booking->fresh());

        $this->assertCount(3, $names);
        $this->assertSame("{$mainBooker->first_name} {$mainBooker->last_name}", $names[0]);
    }
}
