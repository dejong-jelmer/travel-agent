<?php

namespace Tests\Feature;

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
            'name' => fake()->words(2, true),
            'slug' => fake()->slug(),
            'description' => fake()->paragraph(),
            'duration' => fake()->randomDigit(),
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
        $this->assertEquals($tripData['price'], $trip->price);
        $this->assertEquals($tripData['highlights'], $trip->highlights);
        $this->assertTrue($trip->published_at->isSameSecond($tripData['published_at']));
        $this->assertCount(2, $trip->destinations);

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
            'name' => 'Updated trip name',
            'slug' => fake()->slug(),
            'description' => fake()->text(),
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
