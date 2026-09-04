<?php

namespace Tests\Feature;

use App\Enums\Transport;
use App\Enums\Trip\ItemType;
use App\Enums\Trip\PracticalInfo;
use App\Enums\Trip\PriceLabel;
use App\Models\Destination;
use App\Models\Image;
use App\Models\Itinerary;
use App\Models\Trip;
use App\Models\TripItem;
use App\Models\User;
use Database\Seeders\CountrySeeder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class TripTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Collection $destinations;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->actingAs($this->admin);

        $this->seed(CountrySeeder::class);

        Storage::fake(config('images.disk'));
        Storage::makeDirectory(config('images.directory'));

        $this->destinations = Destination::factory(2)->create();
    }

    public function test_admin_can_view_trip_index(): void
    {
        Trip::factory()->count(3)->create();

        $response = $this->get(route('admin.trips.index'));

        $response->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Admin/Trip/Index')
                ->has('trips.data', 3)
                ->has('trips.links')
        );

        $response->assertStatus(200);
    }

    public function test_admin_can_view_trip_create(): void
    {
        $response = $this->get(route('admin.trips.create'));

        $response->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Admin/Trip/Create')
        );

        $response->assertStatus(200);
    }

    public function test_admin_can_create_a_new_trip(): void
    {
        $tripData = [
            'trip_id' => null,
            'name' => fake()->words(2, true),
            'intro' => fake()->text(150),
            'description' => fake()->paragraph(),
            'transport' => [Transport::Train->value],
            'heroImage' => UploadedFile::fake()->image('hero.jpg'),
            'images' => [
                UploadedFile::fake()->image('image1.jpg'),
                UploadedFile::fake()->image('image2.jpg'),
            ],
            'destinations' => $this->destinations->modelKeys(),
            'highlights' => [
                ['title' => 'highlight 1', 'description' => 'description 1'],
                ['title' => 'highlight 2', 'description' => 'description 2'],
                ['title' => 'highlight 3', 'description' => null],
            ],
            'published_at' => now(),
            'meta_title' => fake()->text(60),
            'meta_description' => fake()->text(160),
            'prices' => [
                [
                    'base_price_pp' => fake()->numberBetween(899, 1999),
                    'single_supplement' => fake()->numberBetween(150, 500),
                    'valid_from' => now(),
                    'valid_until' => now()->addMonths(12),
                    'label' => PriceLabel::HighSeason->value,
                ],
            ],
        ];

        $response = $this->post(route('admin.trips.store'), $tripData);

        $trip = Trip::firstOrFail();

        $response->assertRedirect(route('admin.trips.show', $trip));
        $this->assertEquals($tripData['name'], $trip->name);
        $this->assertEquals($tripData['intro'], $trip->intro);
        $this->assertEquals($tripData['description'], $trip->description);
        $this->assertEquals($tripData['highlights'], $trip->highlights);
        $this->assertTrue($trip->published_at->isSameDay($tripData['published_at']));
        $this->assertCount(2, $trip->destinations);

        $expectedTransport = collect($tripData['transport'])->map(fn ($t) => Transport::from($t)->value)->all();
        $this->assertEqualsCanonicalizing($expectedTransport, $trip->transport);

        // Assert prices
        $this->assertEquals(($tripData['prices'][0]['base_price_pp'] * 100), $trip->prices->first()->base_price_pp);
        $this->assertEquals(($tripData['prices'][0]['single_supplement'] * 100), $trip->prices->first()->single_supplement);
        $this->assertEquals($tripData['prices'][0]['label'], $trip->prices->first()->label->value);
        $this->assertTrue($trip->prices->first()->valid_from->isSameDay($tripData['prices'][0]['valid_from']));
        $this->assertTrue($trip->prices->first()->valid_until->isSameDay($tripData['prices'][0]['valid_until']));

        // Assert hero image with hash-based storage
        $heroImage = $trip->heroImage;
        $this->assertNotNull($heroImage);
        $this->assertEquals($tripData['heroImage']->getClientOriginalName(), $heroImage->original_name);
        $this->assertEquals('image/jpeg', $heroImage->mime_type);
        $this->assertTrue($heroImage->is_primary);
        Storage::disk(config('images.disk'))->assertExists(config('images.directory')."/{$heroImage->path}");

        // Assert gallery images with hash-based storage
        $this->assertCount(2, $trip->images);
        foreach ($trip->images as $index => $image) {
            $originalFile = $tripData['images'][$index];
            $this->assertEquals($originalFile->getClientOriginalName(), $image->original_name);
            $this->assertEquals('image/jpeg', $image->mime_type);
            $this->assertFalse($image->is_primary);
            Storage::disk(config('images.disk'))->assertExists(config('images.directory')."/{$image->path}");
        }
    }

    public function test_admin_can_view_trip_edit(): void
    {
        $trip = Trip::factory()->create();

        $response = $this->get(route('admin.trips.edit', $trip));

        $response->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Admin/Trip/Edit')
                ->has('trip')
                ->where('trip.id', $trip->id)
                ->etc()
        );

        $response->assertStatus(200);
    }

    public function test_admin_can_update_an_existing_trip(): void
    {
        $trip = Trip::factory()->create();
        $updateData = [
            'trip_id' => $trip->id,
            'name' => 'Updated trip name',
            'intro' => fake()->text(150),
            'description' => fake()->paragraph(),
            'transport' => array_column([Transport::Bus, Transport::Airplane], 'value'),
            'heroImage' => UploadedFile::fake()->image('updated-featured.jpg'),
            'images' => [
                UploadedFile::fake()->image('new1.jpg'),
                UploadedFile::fake()->image('new2.jpg'),
            ],
            'destinations' => $this->destinations->modelKeys(),
            'highlights' => [
                ['title' => 'updated highlight 1', 'description' => 'updated description 1'],
                ['title' => 'updated highlight 2', 'description' => 'updated description 2'],
                ['title' => 'updated highlight 3', 'description' => null],
            ],
            'published_at' => now()->addDay(fake()->randomDigit())->startOfDay()->toDateTimeString(),
            'meta_title' => fake()->text(60),
            'meta_description' => fake()->text(160),
            'prices' => [
                [
                    'base_price_pp' => fake()->numberBetween(899, 1999),
                    'single_supplement' => fake()->numberBetween(150, 500),
                    'valid_from' => now(),
                    'valid_until' => now()->addMonths(12),
                    'label' => PriceLabel::HighSeason->value,
                ],
            ],
        ];

        $response = $this->post(route('admin.trips.update', $trip), $updateData);
        $trip->refresh();
        $response->assertRedirect(route('admin.trips.show', $trip));

        $this->assertEquals($updateData['name'], $trip->name);
        $this->assertEquals($updateData['intro'], $trip->intro);
        $this->assertEquals($updateData['description'], $trip->description);
        $this->assertEquals($updateData['published_at'], $trip->published_at);
        $this->assertEquals($updateData['meta_title'], $trip->meta_title);
        $this->assertEquals($updateData['meta_description'], $trip->meta_description);

        $expectedTransport = collect($updateData['transport'])->map(fn ($t) => Transport::from($t)->value)->all();
        $this->assertEqualsCanonicalizing($expectedTransport, $trip->transport);

        // Assert prices
        $this->assertEquals(($updateData['prices'][0]['base_price_pp'] * 100), $trip->prices->first()->base_price_pp);
        $this->assertEquals(($updateData['prices'][0]['single_supplement'] * 100), $trip->prices->first()->single_supplement);
        $this->assertEquals($updateData['prices'][0]['label'], $trip->prices->first()->label->value);
        $this->assertTrue($trip->prices->first()->valid_from->isSameDay($updateData['prices'][0]['valid_from']));
        $this->assertTrue($trip->prices->first()->valid_until->isSameDay($updateData['prices'][0]['valid_until']));

        // Assert featured image with hash-based storage
        $heroImage = $trip->heroImage;
        $this->assertNotNull($heroImage);
        $this->assertEquals($updateData['heroImage']->getClientOriginalName(), $heroImage->original_name);
        $this->assertEquals('image/jpeg', $heroImage->mime_type);
        $this->assertTrue($heroImage->is_primary);
        Storage::disk(config('images.disk'))->assertExists(config('images.directory')."/{$heroImage->path}");

        // Assert gallery images with hash-based storage
        $this->assertCount(2, $trip->images);
        foreach ($trip->images as $index => $image) {
            $originalFile = $updateData['images'][$index];
            $this->assertEquals($originalFile->getClientOriginalName(), $image->original_name);
            $this->assertEquals('image/jpeg', $image->mime_type);
            $this->assertFalse($image->is_primary);
            Storage::disk(config('images.disk'))->assertExists(config('images.directory')."/{$image->path}");
        }
    }

    public function test_admin_can_reorder_trip_images(): void
    {
        $trip = Trip::factory()->create();
        foreach (['a.jpg', 'b.jpg', 'c.jpg'] as $order => $path) {
            $trip->images()->create([
                'path' => $path,
                'original_name' => $path,
                'mime_type' => 'image/jpeg',
                'size' => 1000,
                'order' => $order,
            ]);
        }

        $this->assertEquals(['a.jpg', 'b.jpg', 'c.jpg'], $trip->images()->pluck('path')->all());

        $updateData = [
            'trip_id' => $trip->id,
            'name' => $trip->name,
            'intro' => $trip->intro,
            'description' => $trip->description,
            'transport' => array_column([Transport::Train], 'value'),
            'destinations' => $this->destinations->modelKeys(),
            'highlights' => [['title' => 'highlight', 'description' => null]],
            'published_at' => now()->addDay()->startOfDay()->toDateTimeString(),
            'meta_title' => fake()->text(60),
            'meta_description' => fake()->text(160),
            'prices' => [
                [
                    'base_price_pp' => 999,
                    'single_supplement' => 150,
                    'valid_from' => now(),
                    'valid_until' => now()->addMonths(12),
                    'label' => PriceLabel::HighSeason->value,
                ],
            ],
            // Reversed order, one image removed (b.jpg) and a new upload in the middle
            'images' => [
                'c.jpg',
                UploadedFile::fake()->image('new.jpg'),
                'a.jpg',
            ],
        ];

        $response = $this->post(route('admin.trips.update', $trip), $updateData);
        $response->assertRedirect(route('admin.trips.show', $trip));

        $images = $trip->refresh()->images;

        $this->assertCount(3, $images);
        $this->assertEquals([0, 1, 2], $images->pluck('order')->all());
        $this->assertEquals('c.jpg', $images[0]->path);
        $this->assertEquals('new.jpg', $images[1]->original_name);
        $this->assertEquals('a.jpg', $images[2]->path);
        $this->assertDatabaseMissing('images', ['path' => 'b.jpg']);
    }

    public function test_practical_info_accessor_returns_all_keys_with_stored_values(): void
    {
        $trip = Trip::factory()->create();

        $trip->update([
            'practical_info' => [
                'travel_period' => 'Juni - September',
                'transport' => 'Trein',
            ],
        ]);

        $trip->refresh();
        $info = $trip->practical_info;

        foreach (PracticalInfo::cases() as $case) {
            $this->assertArrayHasKey($case->value, $info);
        }

        $this->assertEquals('Juni - September', $info['travel_period']);
        $this->assertEquals('Trein', $info['transport']);
        $this->assertSame('', $info['departure_dates']);
        $this->assertSame('', $info['outbound_return']);
        $this->assertSame('', $info['accommodation']);
    }

    public function test_destinations_formatted_accessor(): void
    {
        $trip = Trip::factory()->create();

        // 0 destinations
        $this->assertEquals('', $trip->destinations_formatted);

        // 1 destination
        $d1 = Destination::factory()->withName('Netherlands')->create();
        $trip->destinations()->attach($d1);
        $trip->load('destinations');
        $this->assertEquals('Netherlands', $trip->destinations_formatted);

        // 2 destinations
        $d2 = Destination::factory()->withName('Belgium')->create();
        $trip->destinations()->attach($d2);
        $trip->load('destinations');
        $this->assertEquals('Netherlands & Belgium', $trip->destinations_formatted);

        // 3 destinations
        $d3 = Destination::factory()->withName('Germany')->create();
        $trip->destinations()->attach($d3);
        $trip->load('destinations');
        $this->assertEquals('Netherlands, Belgium & Germany', $trip->destinations_formatted);
    }

    public function test_destinations_formatted_prefers_region_over_name(): void
    {
        $trip = Trip::factory()->create();
        $destination = Destination::factory()->create(['region' => 'Tuscany', 'name' => 'Italy']);
        $trip->destinations()->attach($destination);
        $trip->load('destinations');

        $this->assertEquals('Tuscany, Italy', $trip->destinations_formatted);
    }

    // Highlights validation tests
    public function test_trip_update_drops_blank_highlights(): void
    {
        $trip = Trip::factory()->create();
        $payload = $this->generateTripUpdatePayload($trip, [
            'highlights' => [
                ['title' => 'highlight 1', 'description' => 'description 1'],
                ['title' => 'highlight 2', 'description' => ''],
                ['title' => '', 'description' => ''],
            ],
        ]);

        $response = $this->post(route('admin.trips.update', $trip), $payload);

        $response->assertSessionHasNoErrors();
        $this->assertEquals([
            ['title' => 'highlight 1', 'description' => 'description 1'],
            ['title' => 'highlight 2', 'description' => null],
        ], $trip->fresh()->highlights);
    }

    public function test_trip_update_rejects_a_description_without_title(): void
    {
        $trip = Trip::factory()->create();
        $payload = $this->generateTripUpdatePayload($trip, [
            'highlights' => [
                ['title' => '', 'description' => 'description without a title'],
            ],
        ]);

        $response = $this->post(route('admin.trips.update', $trip), $payload);

        $response->assertSessionHasErrors('highlights.0.title');
    }

    // Blocked dates validation tests
    public function test_trip_update_accepts_a_single_blocked_date(): void
    {
        $trip = Trip::factory()->create();
        $payload = $this->generateTripUpdatePayload($trip, [
            'blocked_dates' => [
                'dates' => [now()->addMonth()->format('Y-m-d')],
                'weekdays' => [],
            ],
        ]);

        $response = $this->post(route('admin.trips.update', $trip), $payload);

        $response->assertSessionHasNoErrors();
    }

    public function test_trip_update_accepts_a_blocked_date_range(): void
    {
        $trip = Trip::factory()->create();
        $payload = $this->generateTripUpdatePayload($trip, [
            'blocked_dates' => [
                'dates' => [
                    ['start' => now()->addMonth()->format('Y-m-d'), 'end' => now()->addMonths(2)->format('Y-m-d')],
                ],
                'weekdays' => [],
            ],
        ]);

        $response = $this->post(route('admin.trips.update', $trip), $payload);

        $response->assertSessionHasNoErrors();
    }

    public function test_trip_update_accepts_blocked_weekdays(): void
    {
        $trip = Trip::factory()->create();
        $payload = $this->generateTripUpdatePayload($trip, [
            'blocked_dates' => [
                'dates' => [],
                'weekdays' => [0, 6],
            ],
        ]);

        $response = $this->post(route('admin.trips.update', $trip), $payload);

        $response->assertSessionHasNoErrors();
    }

    public function test_trip_update_accepts_null_blocked_dates(): void
    {
        $trip = Trip::factory()->create();
        $payload = $this->generateTripUpdatePayload($trip, ['blocked_dates' => null]);

        $response = $this->post(route('admin.trips.update', $trip), $payload);

        $response->assertSessionHasNoErrors();
    }

    public function test_trip_update_rejects_a_blocked_date_in_the_past(): void
    {
        $trip = Trip::factory()->create();
        $payload = $this->generateTripUpdatePayload($trip, [
            'blocked_dates' => [
                'dates' => [now()->subDay()->format('Y-m-d')],
                'weekdays' => [],
            ],
        ]);

        $response = $this->post(route('admin.trips.update', $trip), $payload);

        $response->assertSessionHasErrors('blocked_dates.dates.0');
    }

    public function test_trip_update_rejects_invalid_weekday(): void
    {
        $trip = Trip::factory()->create();
        $payload = $this->generateTripUpdatePayload($trip, [
            'blocked_dates' => [
                'dates' => [],
                'weekdays' => [7],
            ],
        ]);

        $response = $this->post(route('admin.trips.update', $trip), $payload);

        $response->assertSessionHasErrors('blocked_dates.weekdays.0');
    }

    public function test_trip_update_rejects_range_with_end_before_start(): void
    {
        $trip = Trip::factory()->create();
        $payload = $this->generateTripUpdatePayload($trip, [
            'blocked_dates' => [
                'dates' => [
                    ['start' => now()->addMonths(2)->format('Y-m-d'), 'end' => now()->addMonth()->format('Y-m-d')],
                ],
                'weekdays' => [],
            ],
        ]);

        $response = $this->post(route('admin.trips.update', $trip), $payload);

        $response->assertSessionHasErrors('blocked_dates.dates.0.end');
    }

    public function test_trip_update_rejects_range_with_start_in_the_past(): void
    {
        $trip = Trip::factory()->create();
        $payload = $this->generateTripUpdatePayload($trip, [
            'blocked_dates' => [
                'dates' => [
                    ['start' => now()->subDay()->format('Y-m-d'), 'end' => now()->addMonth()->format('Y-m-d')],
                ],
                'weekdays' => [],
            ],
        ]);

        $response = $this->post(route('admin.trips.update', $trip), $payload);

        $response->assertSessionHasErrors('blocked_dates.dates.0.start');
    }

    public function test_trip_update_normalizes_missing_dates_to_empty_array(): void
    {
        $trip = Trip::factory()->create();
        $payload = $this->generateTripUpdatePayload($trip, [
            'blocked_dates' => ['weekdays' => [1]],
        ]);

        $response = $this->post(route('admin.trips.update', $trip), $payload);

        $response->assertSessionHasNoErrors();
        $this->assertSame([], $trip->refresh()->blocked_dates['dates']);
    }

    public function test_trip_update_normalizes_missing_weekdays_to_empty_array(): void
    {
        $trip = Trip::factory()->create();
        $payload = $this->generateTripUpdatePayload($trip, [
            'blocked_dates' => ['dates' => [now()->addMonth()->format('Y-m-d')]],
        ]);

        $response = $this->post(route('admin.trips.update', $trip), $payload);

        $response->assertSessionHasNoErrors();
        $this->assertSame([], $trip->refresh()->blocked_dates['weekdays']);
    }

    public function test_meta_description_falls_back_to_plain_text_description(): void
    {
        $trip = Trip::factory()->create([
            'description' => '<p>'.str_repeat('Reis met de trein door Europa. ', 20).'</p>',
            'meta_description' => null,
        ]);

        $metaDescription = $trip->meta_description;

        $this->assertStringNotContainsString('<', $metaDescription);
        $this->assertStringStartsWith('Reis met de trein door Europa.', $metaDescription);
        $this->assertLessThanOrEqual(config('seo.meta_description_max_length'), strlen($metaDescription));
    }

    // Helper Methods

    private function generateTripUpdatePayload(Trip $trip, array $overrides = []): array
    {
        return array_merge([
            'name' => $trip->name,
            'intro' => $trip->intro,
            'description' => $trip->description,
            'published_at' => $trip->published_at->toDateTimeString(),
            'destinations' => $this->destinations->modelKeys(),
            'highlights' => [['title' => 'highlight 1', 'description' => null]],
            'meta_title' => $trip->meta_title,
            'meta_description' => $trip->meta_description,
            'prices' => [
                [
                    'base_price_pp' => fake()->numberBetween(899, 1999),
                    'single_supplement' => fake()->numberBetween(150, 500),
                    'valid_from' => now(),
                    'valid_until' => now()->addMonths(12),
                    'label' => PriceLabel::HighSeason->value,
                ],
            ],
        ], $overrides);
    }

    // Duration auto-calculation tests

    public function test_new_trip_has_zero_duration_without_itineraries(): void
    {
        $trip = Trip::factory()->create();

        $this->assertEquals(0, $trip->duration);
    }

    public function test_duration_is_set_from_day_from_when_itinerary_is_created(): void
    {
        $trip = Trip::factory()->create();

        Itinerary::factory()->create(['trip_id' => $trip->id, 'day_from' => 3, 'day_to' => null]);

        $this->assertEquals(3, $trip->fresh()->duration);
    }

    public function test_duration_uses_day_to_when_set(): void
    {
        $trip = Trip::factory()->create();

        Itinerary::factory()->create(['trip_id' => $trip->id, 'day_from' => 2, 'day_to' => 5]);

        $this->assertEquals(5, $trip->fresh()->duration);
    }

    public function test_duration_reflects_max_across_all_itineraries(): void
    {
        $trip = Trip::factory()->create();

        Itinerary::factory()->create(['trip_id' => $trip->id, 'day_from' => 1, 'day_to' => null]);
        Itinerary::factory()->create(['trip_id' => $trip->id, 'day_from' => 2, 'day_to' => 4]);

        $this->assertEquals(4, $trip->fresh()->duration);
    }

    public function test_duration_recalculates_when_highest_itinerary_is_deleted(): void
    {
        $trip = Trip::factory()->create();

        Itinerary::factory()->create(['trip_id' => $trip->id, 'day_from' => 1, 'day_to' => null, 'order' => 1]);
        $highest = Itinerary::factory()->create(['trip_id' => $trip->id, 'day_from' => 2, 'day_to' => 4, 'order' => 2]);

        $this->assertEquals(4, $trip->fresh()->duration);

        $highest->delete();

        $this->assertEquals(1, $trip->fresh()->duration);
    }

    public function test_duration_recalculates_when_itinerary_is_updated(): void
    {
        $trip = Trip::factory()->create();

        $itinerary = Itinerary::factory()->create(['trip_id' => $trip->id, 'day_from' => 1, 'day_to' => 3]);
        $this->assertEquals(3, $trip->fresh()->duration);

        $itinerary->update(['day_to' => 7]);

        $this->assertEquals(7, $trip->fresh()->duration);
    }

    public function test_admin_can_softdelete_a_trip(): void
    {
        $trip = Trip::factory()->create();
        $image = Image::factory()->create([
            'imageable_id' => $trip->id,
            'imageable_type' => Trip::class,
        ]);

        foreach ([ItemType::Inclusion, ItemType::Exclusion] as $type) {
            TripItem::create([
                'trip_id' => $trip->id,
                'type' => $type,
                'item' => fake()->sentence(),
            ]);
        }

        $response = $this->delete(route('admin.trips.destroy', $trip));

        $response->assertRedirect(route('admin.trips.index'));

        $this->assertSoftDeleted($trip);
        $this->assertSoftDeleted($image);

        $this->assertDatabaseMissing('trips', [
            'id' => $trip->id,
            'deleted_at' => null,
        ]);
        $this->assertDatabaseMissing('images', [
            'imageable_id' => $trip->id,
            'deleted_at' => null,
        ]);
        $this->assertDatabaseMissing('trip_items', [
            'trip_id' => $trip->id,
        ]);
    }
}
