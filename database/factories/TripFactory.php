<?php

namespace Database\Factories;

use App\Enums\ImageRelation;
use App\Models\Country;
use App\Models\Image;
use App\Models\Itinerary;
use App\Models\Trip;
use App\Services\CountryService;
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
        $city = fake()->city();
        $duration = fake()->numberBetween(6, 14);

        return [
            'name' => $city,
            'slug' => $this->generateSlug($city),
            'description' => $this->generateDescription($city),
            'price' => randomPrice(995, 12000),
            'duration' => $duration,
            'featured' => true,
            'published_at' => today()->toDateTimeString(),
            'meta_title' => $this->generateMetaTitle($city, $duration),
            'meta_description' => fake()->text(160),
        ];
    }

    private function generateSlug(string $city, ?string $country = null): string
    {
        if ($country) {
            return Str::slug("reis-naar-{$city}-{$country}");
        }

        return Str::slug("reis-naar-{$city}");
    }

    private function generateDescription(string $city, ?string $country = null): string
    {
        $intro = $country
            ? "Ontdek het prachtige {$city} in {$country}. "
            : "Ontdek het prachtige {$city}. ";

        $secondLine = 'Deze bijzondere reis brengt u naar de mooiste plekken en verborgen pareltjes. ';

        return $intro.$secondLine.fake()->paragraph();
    }

    private function generateMetaTitle(string $city, int $duration, ?string $country = null): string
    {
        $title = $country
            ? "Reis naar {$city}, {$country} | {$duration} dagen"
            : "Reis naar {$city} | {$duration} dagen";

        return Str::substr($title, 0, 60);
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

    /**
     * Attach random country from existing pool
     * Requires countries to be seeded first (via CountrySeeder)
     */
    public function withCountry(): static
    {
        return $this->afterCreating(function (Trip $trip) {
            if ($country = Country::inRandomOrder()->first()) {
                $trip->countries()->attach($country);

                // Get locale from CountryService
                $locale = CountryService::getLocale($country->name);
                $city = fake($locale)->city();

                $trip->update([
                    'name' => $city,
                    'slug' => $this->generateSlug($city, $country->name),
                    'description' => $this->generateDescription($city, $country->name),
                    'meta_title' => $this->generateMetaTitle($city, $trip->duration, $country->name),
                ]);
            }
        });
    }
}
