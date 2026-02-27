<?php

namespace Tests\Feature;

use App\Enums\Transport;
use App\Enums\Trip\ItemCategory;
use App\Enums\Trip\PracticalInfo;
use App\Models\Destination;
use App\Models\Image;
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
            'slug' => fake()->slug(),
            'description' => fake()->paragraph(),
            'duration' => fake()->randomDigit(),
            'transport' => [Transport::Train->value],
            'price' => randomPrice(500, 5000),
            'heroImage' => UploadedFile::fake()->image('hero.jpg'),
            'images' => [
                UploadedFile::fake()->image('image1.jpg'),
                UploadedFile::fake()->image('image2.jpg'),
            ],
            'destinations' => $this->destinations->modelKeys(),
            'highlights' => ['highlight 1', 'highlight 2', 'highlight 3'],
            'published_at' => now(),
            'meta_title' => fake()->text(60),
            'meta_description' => fake()->text(160),
        ];

        $response = $this->post(route('admin.trips.store'), $tripData);

        $trip = Trip::firstOrFail();

        $response->assertRedirect(route('admin.trips.show', $trip));
        $this->assertEquals($tripData['name'], $trip->name);
        $this->assertEquals($tripData['slug'], $trip->slug);
        $this->assertEquals($tripData['description'], $trip->description);
        $this->assertEquals($tripData['duration'], $trip->duration);
        $this->assertEquals($tripData['price'], $trip->price);
        $this->assertEquals($tripData['highlights'], $trip->highlights);
        $this->assertTrue($trip->published_at->isSameSecond($tripData['published_at']));
        $this->assertCount(2, $trip->destinations);

        $expectedTransport = collect($tripData['transport'])->map(fn ($t) => Transport::from($t)->value)->all();
        $this->assertEqualsCanonicalizing($expectedTransport, $trip->transport);

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
            'slug' => fake()->slug(),
            'description' => fake()->text(),
            'transport' => array_column([Transport::Bus, Transport::Airplane], 'value'),
            'duration' => fake()->randomDigit(),
            'price' => randomPrice(),
            'heroImage' => UploadedFile::fake()->image('updated-featured.jpg'),
            'images' => [
                UploadedFile::fake()->image('new1.jpg'),
                UploadedFile::fake()->image('new2.jpg'),
            ],
            'destinations' => $this->destinations->modelKeys(),
            'highlights' => ['updated highlight 1', 'updated highlight 2', 'updated highlight 3'],
            'published_at' => now()->addDay(fake()->randomDigit())->startOfDay()->toDateTimeString(),
            'meta_title' => fake()->text(60),
            'meta_description' => fake()->text(160),
        ];

        $response = $this->post(route('admin.trips.update', $trip), $updateData);

        $response->assertRedirect(route('admin.trips.show', $trip));
        $trip->refresh();

        $this->assertEquals($updateData['name'], $trip->name);
        $this->assertEquals($updateData['description'], $trip->description);
        $this->assertEquals($updateData['slug'], $trip->slug);
        $this->assertEquals($updateData['duration'], $trip->duration);
        $this->assertEquals($updateData['price'], $trip->price);
        $this->assertEquals($updateData['published_at'], $trip->published_at);
        $this->assertEquals($updateData['meta_title'], $trip->meta_title);
        $this->assertEquals($updateData['meta_description'], $trip->meta_description);

        $expectedTransport = collect($updateData['transport'])->map(fn ($t) => Transport::from($t)->value)->all();
        $this->assertEqualsCanonicalizing($expectedTransport, $trip->transport);

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

        $this->assertEquals('Tuscany', $trip->destinations_formatted);
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

    // Helper Methods

    private function generateTripUpdatePayload(Trip $trip, array $overrides = []): array
    {
        return array_merge([
            'name' => $trip->name,
            'slug' => $trip->slug,
            'description' => $trip->description,
            'duration' => $trip->duration,
            'price' => $trip->price,
            'published_at' => $trip->published_at->toDateTimeString(),
            'destinations' => $this->destinations->modelKeys(),
            'highlights' => ['highlight 1'],
            'meta_title' => $trip->meta_title,
            'meta_description' => $trip->meta_description,
        ], $overrides);
    }

    public function test_admin_can_softdelete_a_trip(): void
    {
        $trip = Trip::factory()->create();
        $image = Image::factory()->create([
            'imageable_id' => $trip->id,
            'imageable_type' => Trip::class,
        ]);

        foreach ([ItemCategory::Transport, ItemCategory::Accommodation] as $category) {
            TripItem::create([
                'trip_id' => $trip->id,
                'type' => $category->type(),
                'category' => $category,
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
