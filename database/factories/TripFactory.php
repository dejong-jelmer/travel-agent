<?php

namespace Database\Factories;

use App\Enums\ImageRelation;
use App\Enums\Trip\ItemCategory;
use App\Enums\Trip\PracticalInfo;
use App\Models\Destination;
use App\Models\Image;
use App\Models\Itinerary;
use App\Models\Trip;
use App\Models\TripItem;
use App\Services\CountryService;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Trip>
 */
class TripFactory extends Factory
{
    private const HIGHLIGHTS = [
        'Romantische treinrit door de Alpen met adembenemende uitzichten',
        'Bezoek aan historische kastelen en unieke UNESCO werelderfgoed locaties',
        'Lokale culinaire ervaringen en wijnproeverijen',
        'Duurzaam reizen per trein',
    ];

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
            'highlights' => fake()->optional()->randomElements(self::HIGHLIGHTS, fake()->numberBetween(1, 4)) ?? [],
            'meta_title' => $this->generateMetaTitle($city, $duration),
            'meta_description' => fake()->text(160),
            'blocked_dates' => [
                'dates' => [
                    now()->addDays(fake()->numberBetween(1, 10))->format('Y-m-d'),
                    now()->addDays(fake()->numberBetween(10, 30))->format('Y-m-d'),
                    ['start' => now()->addDays(fake()->numberBetween(40, 80))->format('Y-m-d'), 'end' => now()->addDays(fake()->numberBetween(81, 105))->format('Y-m-d')],
                ],
                'weekdays' => fake()->randomElements(range(0, 6)),
            ],
        ];
    }

    private function generateSlug(string $city, ?string $destination = null): string
    {
        if ($destination) {
            return Str::slug("reis-naar-{$city}-{$destination}");
        }

        return Str::slug("reis-naar-{$city}");
    }

    private function generateDescription(string $city, ?string $destination = null): string
    {
        $intro = $destination
            ? "Ontdek het prachtige {$city} in {$destination}. "
            : "Ontdek het prachtige {$city}. ";

        $secondLine = 'Deze bijzondere reis brengt u naar de mooiste plekken en verborgen pareltjes. ';

        return $intro.$secondLine.fake()->paragraph();
    }

    private function generateMetaTitle(string $city, int $duration, ?string $destination = null): string
    {
        $title = $destination
            ? "Reis naar {$city}, {$destination} | {$duration} dagen"
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
     * Attach random destination from existing pool
     * Requires destinations to be seeded first (via DestinationSeeder)
     */
    public function withDestination(): static
    {
        return $this->afterCreating(function (Trip $trip) {
            if ($destination = Destination::inRandomOrder()->first()) {
                $trip->destinations()->attach($destination);

                // Get locale from CountryService using country_code and region
                $locale = CountryService::getLocaleByCountryCode(
                    $destination->country_code,
                );
                $city = fake($locale)->city();

                $trip->update([
                    'name' => $city,
                    'slug' => $this->generateSlug($city, $destination->name),
                    'description' => $this->generateDescription($city, $destination->name),
                    'meta_title' => $this->generateMetaTitle($city, $trip->duration, $destination->name),
                ]);
            }
        });
    }

    public function withItems(): static
    {
        return $this->afterCreating(function (Trip $trip) {
            foreach ([ItemCategory::Transport, ItemCategory::Accommodation, ItemCategory::AdditionalCost] as $category) {
                TripItem::create([
                    'trip_id' => $trip->id,
                    'type' => $category->type(),
                    'category' => $category,
                    'item' => fake()->sentence(),
                ]);
            }
        });
    }

    /**
     * Update trip with 'practical infromation' section headers
     * defined in App\Enums\Trip\PracticalInfo
     */
    public function withPracticalInfo(): static
    {
        return $this->afterCreating(function (Trip $trip) {
            $trip->update([
                'practical_info' => collect(PracticalInfo::cases())
                    ->mapWithKeys(fn ($case) => [
                        $case->value => fake()->text(fake()->numberBetween(50, 250)),
                    ])
                    ->all(),
            ]);
        });
    }
}
