<?php

namespace Database\Factories;

use App\Enums\Booking\PaymentStatus;
use App\Enums\Booking\Status;
use App\Enums\TravelerType;
use App\Models\Booking;
use App\Models\BookingContact;
use App\Models\BookingTraveler;
use App\Models\Trip;
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
        $statuses = $this->getRealisticStatusCombination();

        return [
            'uuid' => fake()->uuid(),
            'trip_id' => Trip::factory(),
            'departure_date' => fake()->dateTimeBetween('now', '+5 months'),
            'has_accepted_conditions' => true,
            'has_confirmed' => true,
            'status' => $statuses['status'],
            'payment_status' => $statuses['payment_status'],
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Booking $booking) {
            $year = now()->format('Y');
            $booking->reference = "{$year}-".str_pad($booking->id, 6, '0', STR_PAD_LEFT);
        });
    }

    public function asNew(): static
    {
        return $this->state(fn () => [
            'status' => Status::New,
            'payment_status' => PaymentStatus::Pending,
        ]);
    }

    public function asPending(): static
    {
        return $this->state(fn () => [
            'status' => Status::Pending,
            'payment_status' => fake()->randomElement([
                PaymentStatus::Pending,
                PaymentStatus::PartiallyPaid,
            ]),
        ]);
    }

    public function asConfirmed(): static
    {
        return $this->state(fn () => [
            'status' => Status::Confirmed,
            'payment_status' => fake()->randomElement([
                PaymentStatus::Paid,
                PaymentStatus::PartiallyPaid,
            ]),
        ]);
    }

    public function asCanceled(): static
    {
        return $this->state(fn () => [
            'status' => Status::Canceled,
            'payment_status' => fake()->randomElement([
                PaymentStatus::Refunded,
                PaymentStatus::PartiallyRefunded,
                PaymentStatus::Pending,
            ]),
        ]);
    }

    public function asCompleted(): static
    {
        return $this->state(fn () => [
            'status' => Status::Completed,
            'payment_status' => PaymentStatus::Paid,
        ]);
    }

    public function withTravelers(): static
    {
        return $this->withAdultTravelers()->withChildTravelers();
    }

    /**
     * Create adult traveler(s) for the booking
     *
     * @param  int|null  $count  Number of adult travelers (min: 1, default: random 1-2)
     *
     * @throws \InvalidArgumentException
     */
    public function withAdultTravelers(?int $count = null): static
    {
        if ($count !== null && $count < 1) {
            throw new \InvalidArgumentException('Booking requires at least 1 adult traveler');
        }

        return $this->afterCreating(function (Booking $booking) use ($count) {
            $adults = BookingTraveler::factory()
                ->count($count ?? fake()->numberBetween(1, 2))
                ->create([
                    'booking_id' => $booking->id,
                    'type' => TravelerType::Adult,
                    'birthdate' => fake()->dateTimeBetween('-80 years', '-18 years')->format('Y-m-d'),
                ]);

            $this->setMainBookerWithContact($booking, $adults->random());
        });
    }

    /**
     * Create child traveler(s) for the booking
     *
     * @param  int|null  $count  Number of child travelers (min: 0, default: random 0-3)
     */
    public function withChildTravelers(?int $count = null): static
    {
        return $this->afterCreating(function (Booking $booking) use ($count) {
            BookingTraveler::factory()
                ->count($count ?? fake()->numberBetween(0, 3))
                ->create([
                    'booking_id' => $booking->id,
                    'type' => TravelerType::Child,
                    'birthdate' => fake()->dateTimeBetween('-12 years', 'now')->format('Y-m-d'),
                ]);
        });
    }

    /**
     * Set the bookings main booker with contact details
     *
     * @param  BookingTraveler  $mainBooker  one of the adults travelers
     * @return static
     */
    private function setMainBookerWithContact(Booking $booking, BookingTraveler $mainBooker): void
    {
        $booking->main_booker_id = $mainBooker->id;
        $booking->save();

        BookingContact::factory()->create([
            'booking_id' => $booking->id,
            'name' => $mainBooker->full_name,
        ]);
    }

    /**
     * Get a realistic status combination
     *
     * @return array{status: Status, payment_status: PaymentStatus}
     */
    private function getRealisticStatusCombination(): array
    {
        $combinations = [
            // New (20%)
            ['status' => Status::New, 'payment_status' => PaymentStatus::Pending, 'weight' => 15],
            ['status' => Status::New, 'payment_status' => PaymentStatus::Failed, 'weight' => 5],

            // Pending (15%)
            ['status' => Status::Pending, 'payment_status' => PaymentStatus::Pending, 'weight' => 8],
            ['status' => Status::Pending, 'payment_status' => PaymentStatus::PartiallyPaid, 'weight' => 7],

            // Confirmed (40%)
            ['status' => Status::Confirmed, 'payment_status' => PaymentStatus::Paid, 'weight' => 30],
            ['status' => Status::Confirmed, 'payment_status' => PaymentStatus::PartiallyPaid, 'weight' => 10],

            // Completed (20%)
            ['status' => Status::Completed, 'payment_status' => PaymentStatus::Paid, 'weight' => 20],

            // Canceled (5%)
            ['status' => Status::Canceled, 'payment_status' => PaymentStatus::Refunded, 'weight' => 2],
            ['status' => Status::Canceled, 'payment_status' => PaymentStatus::PartiallyRefunded, 'weight' => 2],
        ];

        $totalWeight = array_sum(array_column($combinations, 'weight'));
        $random = fake()->numberBetween(1, $totalWeight);

        $currentWeight = 0;
        foreach ($combinations as $combination) {
            $currentWeight += $combination['weight'];
            if ($random <= $currentWeight) {
                return [
                    'status' => $combination['status'],
                    'payment_status' => $combination['payment_status'],
                ];
            }
        }

        // Fallback
        return ['status' => Status::Confirmed, 'payment_status' => PaymentStatus::Paid];
    }
}
