<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\Image;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class TripTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private array $tripData;

    private Collection $countries;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->actingAs($this->admin);

        Storage::fake(config('images.disk'));
        Storage::makeDirectory(config('images.directory'));

        $this->countries = Country::factory(2)->create();

        $this->tripData = [
            'name' => fake()->words(2, true),
            'slug' => fake()->slug(),
            'description' => fake()->paragraph(),
            'duration' => fake()->randomDigit(),
            'price' => fake()->randomFloat(2, 500, 5000),
            'featuredImage' => UploadedFile::fake()->image('featured.jpg'),
            'images' => [
                UploadedFile::fake()->image('image1.jpg'),
                UploadedFile::fake()->image('image2.jpg'),
            ],
            'countries' => $this->countries->modelKeys(),
        ];
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

    public function test_admin_can_view_strip_create(): void
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
        $response = $this->post(route('admin.trips.store'), $this->tripData);

        $trip = Trip::firstOrFail();
        $response->assertRedirect(route('admin.trips.show', $trip));

        $this->assertDatabaseHas('trips', Arr::except($this->tripData, ['featuredImage', 'images', 'countries']));

        // Assert featured image with hash-based storage
        $featuredImage = $trip->featuredImage;
        $this->assertNotNull($featuredImage);
        $this->assertEquals($this->tripData['featuredImage']->getClientOriginalName(), $featuredImage->original_name);
        $this->assertEquals('image/jpeg', $featuredImage->mime_type);
        $this->assertTrue($featuredImage->featured);
        Storage::disk(config('images.disk'))->assertExists(config('images.directory')."/{$featuredImage->path}");

        // Assert gallery images with hash-based storage
        $this->assertCount(2, $trip->images);
        foreach ($trip->images as $index => $image) {
            $originalFile = $this->tripData['images'][$index];
            $this->assertEquals($originalFile->getClientOriginalName(), $image->original_name);
            $this->assertEquals('image/jpeg', $image->mime_type);
            $this->assertFalse($image->featured);
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
            'name' => 'Updated name',
            'slug' => 'Updated slug',
            'description' => 'Updated description',
            'duration' => 5,
            'price' => 1234.56,
            'featuredImage' => UploadedFile::fake()->image('updated-featured.jpg'),
            'images' => [
                UploadedFile::fake()->image('new1.jpg'),
                UploadedFile::fake()->image('new2.jpg'),
            ],
            'countries' => $this->countries->modelKeys(),
        ];

        $response = $this->post(route('admin.trips.update', $trip), $updateData);

        $response->assertRedirect(route('admin.trips.show', $trip));
        $trip->refresh();

        $this->assertEquals('Updated name', $trip->name);
        $this->assertEquals('Updated description', $trip->description);
        $this->assertEquals('Updated slug', $trip->slug);
        $this->assertEquals(5, $trip->duration);
        $this->assertEquals(1234.56, $trip->raw_price);

        // Assert featured image with hash-based storage
        $featuredImage = $trip->featuredImage;
        $this->assertNotNull($featuredImage);
        $this->assertEquals($updateData['featuredImage']->getClientOriginalName(), $featuredImage->original_name);
        $this->assertEquals('image/jpeg', $featuredImage->mime_type);
        $this->assertTrue($featuredImage->featured);
        Storage::disk(config('images.disk'))->assertExists(config('images.directory')."/{$featuredImage->path}");

        // Assert gallery images with hash-based storage
        $this->assertCount(2, $trip->images);
        foreach ($trip->images as $index => $image) {
            $originalFile = $updateData['images'][$index];
            $this->assertEquals($originalFile->getClientOriginalName(), $image->original_name);
            $this->assertEquals('image/jpeg', $image->mime_type);
            $this->assertFalse($image->featured);
            Storage::disk(config('images.disk'))->assertExists(config('images.directory')."/{$image->path}");
        }
    }

    public function test_admin_can_softdelete_a_trip(): void
    {
        $trip = Trip::factory()->create();
        $image = Image::factory()->create([
            'imageable_id' => $trip->id,
            'imageable_type' => Trip::class,
        ]);

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
    }
}
