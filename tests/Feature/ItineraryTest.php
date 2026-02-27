<?php

namespace Tests\Feature;

use App\Models\Image;
use App\Models\Itinerary;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class ItineraryTest extends TestCase
{
    use RefreshDatabase;

    private Trip $trip;

    private Itinerary $itinerary;

    protected function setUp(): void
    {
        parent::setUp();

        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        Storage::fake(config('images.disk'));
        Storage::makeDirectory(config('images.directory'));

        $this->trip = Trip::factory()
            ->has(Itinerary::factory()->count(8), 'itineraries')
            ->create();

        $this->itinerary = $this->trip->itineraries->firstOrFail();
    }

    public function test_admin_can_view_trip_itinerary_index(): void
    {
        $response = $this->get(route('admin.trips.itineraries.index', $this->trip));

        $response->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Admin/Trip/Itinerary/Index')
                ->has('trip')
                ->has('trip.itineraries', $this->trip->itineraries->count())
                ->where('trip.itineraries.0.id', $this->trip->itineraries->first()->id)
                ->where('trip.itineraries.0.title', $this->trip->itineraries->first()->title)
        );

        $response->assertStatus(200);
    }

    public function test_admin_can_update_itinerary_order(): void
    {
        $payload = [
            'itineraries' => $this->trip->itineraries
                ->map(fn ($i, $index) => ['id' => $i->id, 'order' => $index + 1])
                ->toArray(),
        ];

        $response = $this->patch(route('admin.trips.itineraries.order', $this->trip), $payload);

        $response
            ->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_admin_can_view_itinerary_create(): void
    {
        $response = $this->get(route('admin.trips.itineraries.create', $this->trip));

        $response->assertInertia(
            fn (AssertableInertia $page) => $page->component('Admin/Trip/Itinerary/Create')
        );

        $response->assertStatus(200);
    }

    public function test_admin_can_create_a_new_itinerary(): void
    {
        $itineraryData = [
            'title' => fake()->city().' - '.fake()->city(),
            'description' => fake()->text(500),
            'day_from' => fake()->numberBetween(1, 4),
            'day_to' => fake()->optional()->numberBetween(5, 8),
            'image' => UploadedFile::fake()->image('itinerary-image.jpg'),
            'remark' => fake()->words(10, true),
        ];

        $trip = Trip::factory()->create();

        $response = $this->post(route('admin.trips.itineraries.store', $trip), $itineraryData);
        $response->assertRedirect(route('admin.trips.itineraries.index', $trip));

        $itinerary = Itinerary::where('trip_id', $trip->id)->firstOrFail();

        $this->assertEquals($itineraryData['title'], $itinerary->title);
        $this->assertEquals($itineraryData['description'], $itinerary->description);
        $this->assertEquals($itineraryData['day_from'], $itinerary->day_from);
        $this->assertEquals($itineraryData['day_to'], $itinerary->day_to);
        $this->assertEquals($itineraryData['remark'], $itinerary->remark);

        $image = $itinerary->image;
        $this->assertNotNull($image);
        $this->assertEquals($itineraryData['image']->getClientOriginalName(), $image->original_name);
        $this->assertEquals('image/jpeg', $image->mime_type);
        Storage::disk(config('images.disk'))->assertExists(config('images.directory')."/{$image->path}");
    }

    public function test_admin_can_view_itinerary_edit(): void
    {
        $response = $this->get(route('admin.itineraries.edit', $this->itinerary));

        $response->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Admin/Trip/Itinerary/Edit')
                ->has('itinerary')
                ->where('itinerary.id', $this->itinerary->id)
                ->where('itinerary.title', $this->itinerary->title)
                ->etc()
        );

        $response->assertStatus(200);
    }

    public function test_admin_can_update_an_existing_itinerary(): void
    {
        $itineraryData = [
            'trip_id' => $this->trip->id,
            'title' => fake()->city().' - '.fake()->city(),
            'description' => fake()->text(500),
            'day_from' => fake()->numberBetween(1, 4),
            'day_to' => fake()->optional()->numberBetween(5, 8),
            'image' => UploadedFile::fake()->image('itinerary-image.jpg'),
            'remark' => fake()->words(10, true),
        ];

        $response = $this->post(route('admin.itineraries.update', $this->itinerary), $itineraryData);
        $response->assertRedirect(route('admin.trips.itineraries.index', $this->trip));

        $itinerary = $this->itinerary->fresh();

        $this->assertEquals($itineraryData['title'], $itinerary->title);
        $this->assertEquals($itineraryData['description'], $itinerary->description);
        $this->assertEquals($itineraryData['day_from'], $itinerary->day_from);
        $this->assertEquals($itineraryData['day_to'], $itinerary->day_to);
        $this->assertEquals($itineraryData['remark'], $itinerary->remark);

        $image = $itinerary->image;
        $this->assertNotNull($image);
        $this->assertEquals($itineraryData['image']->getClientOriginalName(), $image->original_name);
        $this->assertEquals('image/jpeg', $image->mime_type);
        Storage::disk(config('images.disk'))->assertExists(config('images.directory')."/{$image->path}");
    }

    public function test_admin_can_softdelete_an_itinerary(): void
    {
        $image = Image::factory()->create([
            'imageable_id' => $this->itinerary->id,
            'imageable_type' => Itinerary::class,
        ]);

        $response = $this->delete(route('admin.itineraries.destroy', $this->itinerary));
        $response->assertRedirect(route('admin.trips.itineraries.index', $this->trip));

        $this->assertSoftDeleted($this->itinerary);
        $this->assertSoftDeleted($image);

        $this->assertDatabaseMissing('itineraries', [
            'id' => $this->itinerary->id,
            'deleted_at' => null,
        ]);
        $this->assertDatabaseMissing('images', [
            'imageable_id' => $this->itinerary->id,
            'imageable_type' => Itinerary::class,
            'deleted_at' => null,
        ]);
    }
}
