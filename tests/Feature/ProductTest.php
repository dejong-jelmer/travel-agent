<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\Product;
use App\Models\User;
use App\Models\Image;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private array $productData;
    private Collection $countries;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->actingAs($this->admin);

        Storage::fake('public');
        Storage::makeDirectory('images');

        $this->countries = Country::factory(2)->create();

        $this->productData = [
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

    public function test_admin_can_view_the_products_index_page(): void
    {
        Product::factory()->count(3)->create();

        $response = $this->get(route('admin.products.index'));

        $response->assertInertia(
            fn(AssertableInertia $page) => $page
                ->component('Admin/Products/Index')
                ->has('products.data', 3)
                ->has('products.links')
        );

        $response->assertStatus(200);
    }

    public function test_admin_can_view_the_product_create_page(): void
    {
        $response = $this->get(route('admin.products.create'));

        $response->assertInertia(
            fn(AssertableInertia $page) => $page
                ->component('Admin/Products/Create')
        );

        $response->assertStatus(200);
    }

    public function test_admin_can_store_a_new_product(): void
    {
        $response = $this->post(route('admin.products.store'), $this->productData);

        $product = Product::firstOrFail();
        $response->assertRedirect(route('admin.products.show', $product));

        $this->assertDatabaseHas('products', Arr::except($this->productData, ['featuredImage', 'images', 'countries']));

        $featuredImagePath = $this->productData['featuredImage']->getClientOriginalName();
        $this->assertDatabaseHas('images', [
            'imageable_id' => $product->id,
            'path' => $featuredImagePath,
            'featured' => true,
        ]);
        Storage::assertExists("images/{$featuredImagePath}");

        foreach ($product->images as $index => $image) {
            $path = $this->productData['images'][$index]->getClientOriginalName();
            $this->assertDatabaseHas('images', [
                'imageable_id' => $product->id,
                'path' => $path,
            ]);
            Storage::assertExists("images/{$path}");
        }
    }

    public function test_admin_can_view_the_product_edit_page(): void
    {
        $product = Product::factory()->create();

        $response = $this->get(route('admin.products.edit', $product));

        $response->assertInertia(
            fn(AssertableInertia $page) => $page
                ->component('Admin/Products/Edit')
                ->has('product')
                ->where('product.id', $product->id)
                ->etc()
        );

        $response->assertStatus(200);
    }

    public function test_admin_can_update_an_existing_product(): void
    {
        $product = Product::factory()->create();
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
            'countries' => $this->countries->modelKeys()
        ];

        $response = $this->post(route('admin.products.update', $product), $updateData);

        $response->assertRedirect(route('admin.products.show', $product));
        $product->refresh();

        $this->assertEquals('Updated name', $product->name);
        $this->assertEquals('Updated description', $product->description);
        $this->assertEquals('Updated slug', $product->slug);
        $this->assertEquals(5, $product->duration);
        $this->assertEquals(1234.56, $product->raw_price);

        $featuredImagePath = $updateData['featuredImage']->getClientOriginalName();
        $this->assertDatabaseHas('images', [
            'imageable_id' => $product->id,
            'path' => $featuredImagePath,
            'featured' => true,
        ]);
        Storage::assertExists("images/{$featuredImagePath}");

        foreach ($product->images as $index => $image) {
            $path = $updateData['images'][$index]->getClientOriginalName();
            $this->assertDatabaseHas('images', [
                'imageable_id' => $product->id,
                'path' => $path,
            ]);
            Storage::assertExists("images/{$path}");
        }
    }

    public function test_admin_can_destroy_a_product(): void
    {
        $product = Product::factory()->create();
        $image = Image::factory()->create([
            'imageable_id' => $product->id,
            'imageable_type' => Product::class,
        ]);

        $response = $this->delete(route('admin.products.destroy', $product));

        $response->assertRedirect(route('admin.products.index'));

        $this->assertSoftDeleted($product);
        $this->assertSoftDeleted($image);

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
            'deleted_at' => null,
        ]);
        $this->assertDatabaseMissing('images', [
            'imageable_id' => $product->id,
            'deleted_at' => null,
        ]);
    }
}
