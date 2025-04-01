<?php

namespace Tests\Feature;

use App\Models\Itinerary;
use App\Models\Product;
use App\Models\User;
use App\Models\Image;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
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

        $this->product = Product::factory()->has(Itinerary::factory()->count(8), 'itineraries')->create();

        $this->itinerary = $this->product->itineraries->first();
    }

    public function test_product_itineraries_shows_product_itineraries(): void
    {
        $response = $this->get(route('products.itineraries.index', $this->product));

        $response->assertInertia(
            fn (AssertableInertia $page) => $page->component('Admin/Products/Itineraries/Index')
                ->has('product')
                ->has('product.itineraries', $this->product->itineraries->count())
        );

        $response->assertStatus(200);
    }

    public function test_products_itineraries_order_updates_product_itinerary_order(): void
    {
        $response = $this->patch(
            route('products.itineraries.order', $this->product),
            [
                'itineraries' => $this->product->itineraries->toArray(),
            ]
        );
        $response
            ->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_admin_can_view_the_itinerary_create_page(): void
    {
        $response = $this->get(route('products.itineraries.create', $this->product));

        $response->assertInertia(
            fn (AssertableInertia $page) => $page->component('Admin/Products/Itineraries/Create')
        );
        $response->assertStatus(200);
    }

    public function test_admin_can_store_a_new_itinerary(): void
    {
        $itineraryData = [
            'title' => fake()->city().' - '.fake()->city(),
            'subtitle' => fake()->words(4, true),
            'description' => fake()->text(500),
            'image' => UploadedFile::fake()->image('itinerary-image.jpg'),
            'remark' => fake()->words(10, true),
        ];

        $product = Product::factory()->create();

        $response = $this->post(route('products.itineraries.store', $product), $itineraryData);
        $response->assertRedirect(route('products.itineraries.index', $product));

        $this->assertDatabaseHas('itineraries', Arr::except($itineraryData, ['image']));
        $storedImagePath = $itineraryData['image']->getClientOriginalName();

        $itinerary = Itinerary::where('product_id', $product->id)->first();
        $this->assertDatabaseHas('images', [
            'imageable_id' => $itinerary->id,
            'path' => $storedImagePath,
        ]);

        Storage::assertExists($storedImagePath);
    }

    public function test_admin_can_show_the_itinerary_edit_page(): void
    {
        $response = $this->get(route('itineraries.edit', $this->itinerary));

        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Admin/Itineraries/Edit')
            ->has('itinerary')
            ->where('itinerary.id', $this->itinerary->id)
            ->etc()
        );

        $response->assertStatus(200);
    }

    public function test_admin_can_update_an_existing_itinerary(): void
    {
        $itineraryData = [
            'title' => fake()->city().' - '.fake()->city(),
            'subtitle' => fake()->words(4, true),
            'description' => fake()->text(500),
            'image' => UploadedFile::fake()->image('itinerary-image.jpg'),
            'remark' => fake()->words(10, true),
        ];

        $response = $this->post(route('itineraries.update', $this->itinerary), $itineraryData);
        $response->assertRedirect(route('products.itineraries.index', $this->product));

        $this->assertDatabaseHas('itineraries', Arr::except($itineraryData, ['image']));
        $storedImagePath = $itineraryData['image']->getClientOriginalName();

        $this->assertDatabaseHas('images', [
            'imageable_id' => $this->itinerary->id,
            'path' => $storedImagePath,
        ]);

        Storage::assertExists($storedImagePath);
    }

    public function test_admin_can_destroy_an_itinerary(): void
    {
        $image = Image::factory()->create([
            'imageable_id' => $this->itinerary->id,
            'imageable_type' => Itinerary::class,
        ]);
        $response = $this->delete(route('itineraries.destroy', $this->itinerary));
        $response->assertRedirect(route('products.itineraries.index', $this->product));

        $this->assertSoftDeleted($this->itinerary);
        $this->assertSoftDeleted($image);
    }
}
