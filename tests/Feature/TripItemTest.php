<?php

namespace Tests\Feature;

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
            'subtitle' => fake()->text(150),
            'description' => fake()->paragraph(),
            'heroImage' => UploadedFile::fake()->image('hero.jpg'),
            'images' => [UploadedFile::fake()->image('img.jpg')],
            'destinations' => $this->destinations->modelKeys(),
            'highlights' => [
                ['title' => 'highlight 1', 'description' => 'description 1'],
                ['title' => 'highlight 2', 'description' => null],
            ],
            'published_at' => now()->toDateTimeString(),
            'meta_title' => fake()->text(60),
            'meta_description' => fake()->text(160),
        ], $overrides);
    }

    public function test_admin_can_create_trip_with_items(): void
    {
        $items = [
            ['type' => ItemType::Inclusion->value, 'item' => 'First class train tickets'],
            ['type' => ItemType::Inclusion->value, 'item' => 'Hotel with breakfast'],
            ['type' => ItemType::Exclusion->value, 'item' => 'Travel insurance'],
        ];

        $response = $this->post(route('admin.trips.store'), $this->baseTripData(['items' => $items]));

        $trip = Trip::latest('id')->firstOrFail();
        $response->assertRedirect(route('admin.trips.show', $trip));

        $this->assertCount(3, $trip->items);

        $this->assertDatabaseHas('trip_items', [
            'trip_id' => $trip->id,
            'type' => ItemType::Inclusion->value,
            'item' => 'First class train tickets',
        ]);

        $this->assertDatabaseHas('trip_items', [
            'trip_id' => $trip->id,
            'type' => ItemType::Exclusion->value,
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
        TripItem::create(['trip_id' => $this->trip->id, 'type' => ItemType::Inclusion, 'item' => 'Old item 1']);
        TripItem::create(['trip_id' => $this->trip->id, 'type' => ItemType::Exclusion, 'item' => 'Old item 2']);

        $newItems = [
            ['type' => ItemType::Inclusion->value, 'item' => 'New hotel stay'],
            ['type' => ItemType::Inclusion->value, 'item' => 'New train tickets'],
            ['type' => ItemType::Exclusion->value, 'item' => 'New insurance fee'],
        ];

        $response = $this->post(route('admin.trips.update', $this->trip), $this->baseTripData(['items' => $newItems]));
        $this->trip->refresh();
        $response->assertRedirect(route('admin.trips.show', $this->trip));

        $this->assertCount(3, $this->trip->items);

        $this->assertDatabaseMissing('trip_items', ['item' => 'Old item 1']);
        $this->assertDatabaseMissing('trip_items', ['item' => 'Old item 2']);
        $this->assertDatabaseHas('trip_items', ['trip_id' => $this->trip->id, 'item' => 'New hotel stay']);
        $this->assertDatabaseHas('trip_items', ['trip_id' => $this->trip->id, 'item' => 'New train tickets']);
        $this->assertDatabaseHas('trip_items', ['trip_id' => $this->trip->id, 'item' => 'New insurance fee']);
    }

    public function test_admin_can_remove_all_items_on_update(): void
    {
        TripItem::create(['trip_id' => $this->trip->id, 'type' => ItemType::Inclusion, 'item' => 'Item to remove 1']);
        TripItem::create(['trip_id' => $this->trip->id, 'type' => ItemType::Inclusion, 'item' => 'Item to remove 2']);
        TripItem::create(['trip_id' => $this->trip->id, 'type' => ItemType::Exclusion, 'item' => 'Item to remove 3']);

        $response = $this->post(route('admin.trips.update', $this->trip), $this->baseTripData(['items' => []]));
        $this->trip->refresh();

        $response->assertRedirect(route('admin.trips.show', $this->trip));

        $this->assertCount(0, TripItem::where('trip_id', $this->trip->id)->get());
    }

    public function test_trip_show_includes_aggregated_items(): void
    {
        TripItem::create(['trip_id' => $this->trip->id, 'type' => ItemType::Inclusion, 'item' => 'Train tickets included']);
        TripItem::create(['trip_id' => $this->trip->id, 'type' => ItemType::Exclusion, 'item' => 'Booking fees']);

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

    public function test_public_trip_show_passes_trip_items_grouped_by_type(): void
    {
        TripItem::create(['trip_id' => $this->trip->id, 'type' => ItemType::Inclusion, 'item' => 'Train tickets included']);
        TripItem::create(['trip_id' => $this->trip->id, 'type' => ItemType::Exclusion, 'item' => 'Booking fees']);
        TripItem::create(['trip_id' => $this->trip->id, 'type' => ItemType::Optional, 'item' => 'Sleeper cabin upgrade']);

        // Trip items are appended to the default items from config
        $defaults = config('trip-default-items');
        $inclusions = count($defaults[ItemType::Inclusion->value]);
        $exclusions = count($defaults[ItemType::Exclusion->value]);

        $inclusion = 'tripItems.'.ItemType::Inclusion->label();
        $exclusion = 'tripItems.'.ItemType::Exclusion->label();
        $optional = 'tripItems.'.ItemType::Optional->label();

        $response = $this->get(route('trips.show', $this->trip));

        $response->assertOk();
        $response->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Trip/Show')
                ->has('tripItems', 3)
                ->has($inclusion, $inclusions + 1)
                ->where("{$inclusion}.0.item", __($defaults[ItemType::Inclusion->value][0]))
                ->where("{$inclusion}.{$inclusions}.item", 'Train tickets included')
                ->where("{$inclusion}.{$inclusions}.type", ItemType::Inclusion->value)
                ->has($exclusion, $exclusions + 1)
                ->where("{$exclusion}.{$exclusions}.item", 'Booking fees')
                ->where("{$exclusion}.{$exclusions}.type", ItemType::Exclusion->value)
                ->has($optional, 1)
                ->where("{$optional}.0.item", 'Sleeper cabin upgrade')
                ->where("{$optional}.0.type", ItemType::Optional->value)
        );
    }

    public function test_trip_item_validation_rejects_invalid_type(): void
    {
        $tripCount = Trip::count();

        $items = [
            ['type' => 'invalid_type', 'item' => 'Some item'],
        ];

        $response = $this->post(route('admin.trips.store'), $this->baseTripData(['items' => $items]));

        $response->assertSessionHasErrors('items.0.type');
        $this->assertEquals($tripCount, Trip::count());
    }
}
