<?php

namespace Tests\Feature;

use App\Enums\Trip\ItemCategory;
use App\Enums\Trip\ItemType;
use App\Models\Destination;
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

class TripItemTest extends TestCase
{
    use RefreshDatabase;

    private Trip $trip;

    private Collection $destinations;

    protected function setUp(): void
    {
        parent::setUp();

        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        $this->seed(CountrySeeder::class);

        Storage::fake(config('images.disk'));
        Storage::makeDirectory(config('images.directory'));

        $this->destinations = Destination::factory(2)->create();
        $this->trip = Trip::factory()->create();
    }

    private function baseTripData(array $overrides = []): array
    {
        return array_merge([
            'name' => fake()->words(2, true),
            'slug' => fake()->slug(),
            'description' => fake()->paragraph(),
            'duration' => fake()->numberBetween(5, 14),
            'price' => randomPrice(500, 5000),
            'heroImage' => UploadedFile::fake()->image('hero.jpg'),
            'images' => [UploadedFile::fake()->image('img.jpg')],
            'destinations' => $this->destinations->modelKeys(),
            'highlights' => ['highlight 1', 'highlight 2'],
            'published_at' => now()->toDateTimeString(),
            'meta_title' => fake()->text(60),
            'meta_description' => fake()->text(160),
        ], $overrides);
    }

    public function test_admin_can_create_trip_with_items(): void
    {
        $items = [
            ['type' => ItemType::Inclusion->value, 'category' => ItemCategory::Transport->value, 'item' => 'First class train tickets'],
            ['type' => ItemType::Inclusion->value, 'category' => ItemCategory::Accommodation->value, 'item' => 'Hotel with breakfast'],
            ['type' => ItemType::Exclusion->value, 'category' => ItemCategory::AdditionalCost->value, 'item' => 'Travel insurance'],
        ];

        $response = $this->post(route('admin.trips.store'), $this->baseTripData(['items' => $items]));

        $trip = Trip::latest('id')->firstOrFail();
        $response->assertRedirect(route('admin.trips.show', $trip));

        $this->assertCount(3, $trip->items);

        $this->assertDatabaseHas('trip_items', [
            'trip_id' => $trip->id,
            'type' => ItemType::Inclusion->value,
            'category' => ItemCategory::Transport->value,
            'item' => 'First class train tickets',
        ]);

        $this->assertDatabaseHas('trip_items', [
            'trip_id' => $trip->id,
            'type' => ItemType::Exclusion->value,
            'category' => ItemCategory::AdditionalCost->value,
            'item' => 'Travel insurance',
        ]);
    }

    public function test_admin_can_create_trip_without_items(): void
    {
        $response = $this->post(route('admin.trips.store'), $this->baseTripData());

        $trip = Trip::latest('id')->firstOrFail();
        $response->assertRedirect(route('admin.trips.show', $trip));

        $this->assertCount(0, $trip->items);
    }

    public function test_admin_can_update_trip_items(): void
    {
        // Create initial items
        TripItem::create(['trip_id' => $this->trip->id, 'type' => ItemType::Inclusion, 'category' => ItemCategory::Transport, 'item' => 'Old item 1']);
        TripItem::create(['trip_id' => $this->trip->id, 'type' => ItemType::Exclusion, 'category' => ItemCategory::AdditionalCost, 'item' => 'Old item 2']);

        $newItems = [
            ['type' => ItemType::Inclusion->value, 'category' => ItemCategory::Accommodation->value, 'item' => 'New hotel stay'],
            ['type' => ItemType::Inclusion->value, 'category' => ItemCategory::Transport->value, 'item' => 'New train tickets'],
            ['type' => ItemType::Exclusion->value, 'category' => ItemCategory::AdditionalCost->value, 'item' => 'New insurance fee'],
        ];

        $response = $this->post(route('admin.trips.update', $this->trip), $this->baseTripData(['items' => $newItems]));

        $response->assertRedirect(route('admin.trips.show', $this->trip));

        $this->trip->refresh();
        $this->assertCount(3, $this->trip->items);

        $this->assertDatabaseMissing('trip_items', ['item' => 'Old item 1']);
        $this->assertDatabaseMissing('trip_items', ['item' => 'Old item 2']);
        $this->assertDatabaseHas('trip_items', ['trip_id' => $this->trip->id, 'item' => 'New hotel stay']);
        $this->assertDatabaseHas('trip_items', ['trip_id' => $this->trip->id, 'item' => 'New train tickets']);
        $this->assertDatabaseHas('trip_items', ['trip_id' => $this->trip->id, 'item' => 'New insurance fee']);
    }

    public function test_admin_can_remove_all_items_on_update(): void
    {
        TripItem::create(['trip_id' => $this->trip->id, 'type' => ItemType::Inclusion, 'category' => ItemCategory::Transport, 'item' => 'Item to remove 1']);
        TripItem::create(['trip_id' => $this->trip->id, 'type' => ItemType::Inclusion, 'category' => ItemCategory::Accommodation, 'item' => 'Item to remove 2']);
        TripItem::create(['trip_id' => $this->trip->id, 'type' => ItemType::Exclusion, 'category' => ItemCategory::AdditionalCost, 'item' => 'Item to remove 3']);

        $response = $this->post(route('admin.trips.update', $this->trip), $this->baseTripData(['items' => []]));

        $response->assertRedirect(route('admin.trips.show', $this->trip));

        $this->assertCount(0, TripItem::where('trip_id', $this->trip->id)->get());
    }

    public function test_trip_show_includes_aggregated_items(): void
    {
        TripItem::create(['trip_id' => $this->trip->id, 'type' => ItemType::Inclusion, 'category' => ItemCategory::Transport, 'item' => 'Train tickets included']);
        TripItem::create(['trip_id' => $this->trip->id, 'type' => ItemType::Exclusion, 'category' => ItemCategory::AdditionalCost, 'item' => 'Booking fees']);

        $response = $this->get(route('admin.trips.show', $this->trip));

        $response->assertStatus(200);
        $response->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Admin/Trip/Show')
                ->has('tripItems')
                ->has('tripItems.'.ItemType::Inclusion->label())
                ->has('tripItems.'.ItemType::Exclusion->label())
        );
    }

    public function test_trip_item_validation_rejects_invalid_category(): void
    {
        $tripCount = Trip::count();

        $items = [
            ['type' => ItemType::Inclusion->value, 'category' => 'invalid_category', 'item' => 'Some item'],
        ];

        $response = $this->post(route('admin.trips.store'), $this->baseTripData(['items' => $items]));

        $response->assertSessionHasErrors('items.0.category');
        $this->assertEquals($tripCount, Trip::count());
    }

    public function test_trip_item_validation_rejects_invalid_type(): void
    {
        $tripCount = Trip::count();

        $items = [
            ['type' => 'invalid_type', 'category' => ItemCategory::Transport->value, 'item' => 'Some item'],
        ];

        $response = $this->post(route('admin.trips.store'), $this->baseTripData(['items' => $items]));

        $response->assertSessionHasErrors('items.0.type');
        $this->assertEquals($tripCount, Trip::count());
    }
}
