<?php

namespace Database\Factories;

use App\Enums\ImageRelation;
use App\Models\Booking;
use App\Models\Country;
use App\Models\Image;
use App\Models\Itinerary;
use App\Models\Trip;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Trip>
 */
class TripFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $country = Country::inRandomOrder()->first();
        while ($country === null) {
            Country::factory()->create();
            $country = Country::inRandomOrder()->first();
        }
        $city = fake()->city();
        $text = fake()->paragraph();
        $duration = fake()->numberBetween(6, 14);

        return [
            'name' => $city,
            'slug' => Str::slug("bijzondere-reis-naar-{$city}-{$country->name}"),
            'description' => "Bijzondere reis, waar u het mooie {$city} bezoek in {$country->name}. {$text}",
            'price' => randomPrice(),
            'duration' => $duration,
            'featured' => true,
            'published_at' => today()->toDateTimeString(),
            'meta_title' => Str::substr("Reis naar {$city} | $duration dagen | {$country->name}", 0, 60),
            'meta_description' => fake()->text(160),
        ];
    }

    public function withBooking(): static
    {
        return $this->has(
            Booking::factory(),
            'bookings'
        );
    }

    public function withHeroImage(): static
    {
        return $this->has(
            Image::factory()->primary(),
            ImageRelation::HeroImage->value
        );
    }

    public function withImages(int $count = 3): static
    {
        return $this->has(
            Image::factory()->count($count),
            ImageRelation::Images->value
        );
    }

    public function withCountries(): static
    {
        return $this->has(
            Country::factory(),
            'countries'
        );
    }

    public function withAnItinerary(): static
    {
        return $this->afterCreating(function (Trip $trip) {
            Itinerary::factory()
                ->withImage()
                ->count($trip->duration)
                ->sequence(fn (Sequence $sequence) => [
                    'order' => $sequence->index + 1,
                ])
                ->create(['trip_id' => $trip->id]);
        });
    }
}
