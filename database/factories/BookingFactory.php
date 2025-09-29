<?php

namespace Database\Factories;

use App\Enums\TravelerType;
use App\Models\Booking;
use App\Models\BookingContact;
use App\Models\BookingTraveler;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => fake()->uuid(),
            'departure_date' => fake()->dateTimeBetween('now', '+5 months'),
            'confirmed' => true,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Booking $booking) {
            $year = now()->format('Y');
            $booking->reference = "{$year}-".str_pad($booking->id, 6, '0', STR_PAD_LEFT);

            $adults = BookingTraveler::factory()
                ->count(2)
                ->create([
                    'booking_id' => $booking->id,
                    'type' => TravelerType::Adult,
                ]);

            BookingTraveler::factory()
                ->count(2)
                ->create([
                    'booking_id' => $booking->id,
                    'type' => TravelerType::Child,
                ]);

            $mainBooker = $adults->random();
            $booking->main_booker_id = $mainBooker->id;

            BookingContact::factory()->create([
                'booking_id' => $booking->id,
                'name' => $mainBooker->full_name,
            ]);
            $booking->save();
        });
    }
}
