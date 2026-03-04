<?php

namespace Database\Factories;

use App\Enums\Booking\PaymentStatus;
use App\Enums\Booking\Status;
use App\Enums\SettingKey;
use App\Models\Booking;
use App\Models\BookingContact;
use App\Models\BookingTraveler;
use App\Models\Trip;
use App\Models\TripPrice;
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
            'trip_price_id' => 0,
            'departure_date' => fake()->dateTimeBetween('now', '+5 months'),
            'has_accepted_conditions' => true,
            'has_confirmed' => true,
            'status' => $statuses['status'],
            'payment_status' => $statuses['payment_status'],
            'price_per_person' => 0,
            'single_supplement' => 0,
            'total_price' => 0,
            'fees_and_funds' => [],
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Booking $booking) {
            $tripPrice = TripPrice::factory()->create(['trip_id' => $booking->trip_id]);
            $year = now()->format('Y');
            $booking->reference = "{$year}-".str_pad($booking->id, 6, '0', STR_PAD_LEFT);
            $booking->trip_price_id = $tripPrice->id;
            $booking->price_per_person = $tripPrice->base_price_pp;
            $booking->single_supplement = $tripPrice->single_supplement;
            $booking->total_price = $tripPrice->base_price_pp;
            $booking->fees_and_funds = [
                SettingKey::BookingFee->value => 2500,
                SettingKey::EmergencyFund->value => 1000,
                SettingKey::GuaranteeFund->value => 250,
            ];
            $booking->saveQuietly();
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

    /**
     * Attach travelers to the booking and calculate the total price.
     *
     * When $adults or $children are not provided, a random count is used.
     * The price is calculated based on price_per_person and, for a single
     * adult, the single_supplement.
     *
     * @param  int|null  $adults  Number of adults (default: 1–4)
     * @param  int|null  $children  Number of children (default: 0–2)
     */
    public function withTravelers(?int $adults = null, ?int $children = null): static
    {
        return $this->afterCreating(function (Booking $booking) use ($adults, $children) {
            $adultCount = $adults ?? rand(1, 4);
            $childCount = $children ?? rand(0, 2);

            $adults = BookingTraveler::factory()->new()->adult()->count($adultCount)->make();
            $booking->travelers()->saveMany($adults);

            $children = $childCount > 0
                ? BookingTraveler::factory()->new()->child()->count($childCount)->make()
                : collect();

            if ($children->isNotEmpty()) {
                $booking->travelers()->saveMany($children);
            }

            $adultPrice = $adultCount === 1
                ? $booking->price_per_person + $booking->single_supplement
                : $booking->price_per_person * $adultCount;

            $booking->total_price = $adultPrice + ($booking->price_per_person * $childCount);
            $booking->saveQuietly();

            $this->setMainBookerWithContact($booking, $adults->random());
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
        $booking->saveQuietly();

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
