<?php

namespace Tests\Feature;

use App\Enums\Meals;
use App\Enums\Transport;
use App\Models\Image;
use App\Models\Itinerary;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class ItineraryTest extends TestCase
{
    use RefreshDatabase;

    private Product $product;

    private Itinerary $itinerary;

    protected function setUp(): void
    {
        parent::setUp();

        $admin = User::factory()->create();
        $this->actingAs($admin);

        Storage::fake('public');
        Storage::makeDirectory('images');

        $this->product = Product::factory()
            ->has(Itinerary::factory()->count(8), 'itineraries')
            ->create();

        $this->itinerary = $this->product->itineraries->firstOrFail();
    }

    public function test_product_itineraries_shows_product_itineraries(): void
    {
        $response = $this->get(route('admin.products.itineraries.index', $this->product));

        $response->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Admin/Products/Itineraries/Index')
                ->has('product')
                ->has('product.itineraries', $this->product->itineraries->count())
                ->where('product.itineraries.0.id', $this->product->itineraries->first()->id)
                ->where('product.itineraries.0.title', $this->product->itineraries->first()->title)
        );

        $response->assertStatus(200);
    }

    public function test_products_itineraries_order_updates_product_itinerary_order(): void
    {
        $payload = [
            'itineraries' => $this->product->itineraries
                ->map(fn ($i, $index) => ['id' => $i->id, 'order' => $index + 1])
                ->toArray(),
        ];

        $response = $this->patch(route('admin.products.itineraries.order', $this->product), $payload);

        $response
            ->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_admin_can_view_the_itinerary_create_page(): void
    {
        $response = $this->get(route('admin.products.itineraries.create', $this->product));

        $response->assertInertia(
            fn (AssertableInertia $page) => $page->component('Admin/Products/Itineraries/Create')
        );

        $response->assertStatus(200);
    }

    public function test_admin_can_store_a_new_itinerary(): void
    {
        $itineraryData = [
            'title' => fake()->city().' - '.fake()->city(),
            'description' => fake()->text(500),
            'location' => fake()->city.', '.fake()->country,
            'image' => UploadedFile::fake()->image('itinerary-image.jpg'),
            'meals' => fake()->randomElements(array_column(Meals::cases(), 'value'), rand(1, 2)),
            'transport' => fake()->randomElements(array_column(Transport::cases(), 'value'), rand(1, 4)),
            'remark' => fake()->words(10, true),
        ];

        $product = Product::factory()->create();

        $response = $this->post(route('admin.products.itineraries.store', $product), $itineraryData);
        $response->assertRedirect(route('admin.products.itineraries.index', $product));

        $itinerary = Itinerary::where('product_id', $product->id)->firstOrFail();

        // ✅ fields
        $this->assertEquals($itineraryData['title'], $itinerary->title);
        $this->assertEquals($itineraryData['description'], $itinerary->description);
        $this->assertEquals($itineraryData['location'], $itinerary->location);
        $this->assertEquals($itineraryData['remark'], $itinerary->remark);

        // ✅ Enums
        $expectedMeals = collect($itineraryData['meals'])->map(fn ($meal) => Meals::from($meal)->value)->all();
        $this->assertEqualsCanonicalizing($expectedMeals, array_map(fn ($m) => $m->value, $itinerary->meals));

        $expectedTransport = collect($itineraryData['transport'])->map(fn ($t) => Transport::from($t)->value)->all();
        $this->assertEqualsCanonicalizing($expectedTransport, array_map(fn ($t) => $t->value, $itinerary->transport));

        // ✅ Image
        $storedImagePath = $itineraryData['image']->getClientOriginalName();
        $this->assertDatabaseHas('images', [
            'imageable_id' => $itinerary->id,
            'path' => $storedImagePath,
        ]);
        Storage::assertExists("images/{$storedImagePath}");
    }

    public function test_admin_can_show_the_itinerary_edit_page(): void
    {
        $response = $this->get(route('admin.itineraries.edit', $this->itinerary));

        $response->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Admin/Products/Itineraries/Edit')
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
            'title' => fake()->city().' - '.fake()->city(),
            'description' => fake()->text(500),
            'location' => fake()->city.', '.fake()->country,
            'image' => UploadedFile::fake()->image('itinerary-image.jpg'),
            'meals' => fake()->randomElements(array_column(Meals::cases(), 'value'), rand(1, 2)),
            'transport' => fake()->randomElements(array_column(Transport::cases(), 'value'), rand(1, 4)),
            'remark' => fake()->words(10, true),
        ];

        $response = $this->post(route('admin.itineraries.update', $this->itinerary), $itineraryData);
        $response->assertRedirect(route('admin.products.itineraries.index', $this->product));

        $itinerary = $this->itinerary->fresh();

        // ✅ fields
        $this->assertEquals($itineraryData['title'], $itinerary->title);
        $this->assertEquals($itineraryData['description'], $itinerary->description);
        $this->assertEquals($itineraryData['location'], $itinerary->location);
        $this->assertEquals($itineraryData['remark'], $itinerary->remark);

        // ✅ Enums
        $expectedMeals = collect($itineraryData['meals'])->map(fn ($meal) => Meals::from($meal)->value)->all();
        $this->assertEqualsCanonicalizing($expectedMeals, array_map(fn ($m) => $m->value, $itinerary->meals));

        $expectedTransport = collect($itineraryData['transport'])->map(fn ($t) => Transport::from($t)->value)->all();
        $this->assertEqualsCanonicalizing($expectedTransport, array_map(fn ($t) => $t->value, $itinerary->transport));

        // ✅ Images
        $storedImagePath = $itineraryData['image']->getClientOriginalName();
        $this->assertDatabaseHas('images', [
            'imageable_id' => $itinerary->id,
            'path' => $storedImagePath,
        ]);
        Storage::assertExists("images/{$storedImagePath}");
    }

    public function test_admin_can_destroy_an_itinerary(): void
    {
        $image = Image::factory()->create([
            'imageable_id' => $this->itinerary->id,
            'imageable_type' => Itinerary::class,
        ]);

        $response = $this->delete(route('admin.itineraries.destroy', $this->itinerary));
        $response->assertRedirect(route('admin.products.itineraries.index', $this->product));

        // ✅ Soft deletes
        $this->assertSoftDeleted($this->itinerary);
        $this->assertSoftDeleted($image);

        // ✅ Extra check
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
