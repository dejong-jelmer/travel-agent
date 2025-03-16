<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Country;
use App\Models\Product;
use App\Models\ProductImage;
use Inertia\Testing\AssertableInertia;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;


class ProductTest extends TestCase
{

    use RefreshDatabase;

    public function test_admin_can_view_the_product_index_page(): void
    {
        $admin = User::factory()->create();
        $this->actingAs($admin);

        $country = Country::factory()->create();
        $products = Product::factory()
            ->count(3)
            ->has(ProductImage::factory()->count(2), 'images')
            ->create(['country_id' => $country->id, 'price' => 895.00]);

        $response = $this->get(route('products.index'));

        $response->assertInertia(
            fn(AssertableInertia $page) =>
            $page->component('Admin/Products/Index')
                ->has('products', 3)
                ->where('products.0.id', $products[0]->id)
                ->where('products.0.country.id', $country->id)
                ->where('products.0.price', (string) number_format((float) $products[0]->price, 2, '.', ''))
                ->has('products.0.images', 2)
                ->where('products.0.images.0.id', $products[0]->images[0]->id)
        );

        $response->assertStatus(200);
    }

    public function test_admin_can_view_the_product_create_page(): void
    {
        $admin = User::factory()->create();
        $this->actingAs($admin);

        $response = $this->get(route('products.create'));

        $response->assertInertia(
            fn(AssertableInertia $page) =>
            $page->component('Admin/Products/Create')
        );
        $response->assertStatus(200);
    }

    public function test_admin_can_store_a_new_product(): void
    {
        $admin = User::factory()->create();
        $this->actingAs($admin);

        $country = Country::factory()->create();
        $description = fake()->text();

        Storage::fake('public');
        Storage::makeDirectory('images/products/featured');
        $image = UploadedFile::fake()->image("image0.jpg");

        $productData = [
            'name' => 'Test Product',
            'slug' => 'test-slug-for-product',
            'description' => $description,
            'price' => "895.00",
            'duration' => 8,
            'image' => $image,
            'images' => [
                UploadedFile::fake()->image('image1.jpg'),
                UploadedFile::fake()->image('image2.jpg'),
            ],
            'country_id' => $country->id
        ];

        $response = $this->post(route('products.store'), $productData);
        $response->assertRedirect(route('products.index'));

        $this->assertDatabaseHas('products', Arr::except($productData, ['image', 'images']));

        $product = Product::first();
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'image' => "images/products/featured/{$productData['image']->hashName()}",
        ]);

        Storage::disk('public')->assertExists($product->getRawOriginal('image'));
        $this->assertCount(2, $product->images);

        foreach ($product->images as $index => $image) {
            $path = "images/products/{$productData['images'][$index]->hashName()}";
            $this->assertDatabaseHas('product_images', [
                'product_id' => $product->id,
                'path' => $path,
            ]);
            Storage::disk('public')->assertExists($path);
        }

    }

    public function test_admin_can_show_singel_product(): void
    {
        $admin = User::factory()->create();
        $this->actingAs($admin);

        $country = Country::factory()->create();
        $product = Product::factory()
            ->has(ProductImage::factory()->count(3), 'images')
            ->create(['country_id' => $country->id]);

        $response = $this->get(route('products.show', $product));
        $response->assertInertia(
            fn(AssertableInertia $page) =>
                $page->component('Admin/Products/Show')
                    ->has('product')
                    ->where('product.id', $product->id)
                    ->where('product.name', $product->name)
                    ->where('product.slug', $product->slug)
                    ->where('product.duration', $product->duration)
                    ->where('product.price', (string) number_format((float) $product->price, 2, '.', ''))
                    ->where('product.active', $product->active)
                    ->where('product.featured', $product->featured)
                    ->where('product.published_at', $product->published_at->format('Y-m-d H:i:s'))
                    ->where('product.country_id', $product->country->id)
                    ->has('product.images', 3)

            );

        $response->assertStatus(200);
    }

    public function test_admin_can_render_the_product_edit_page(): void
    {
        $admin = User::factory()->create();
        $this->actingAs($admin);

        $country = Country::factory()->create();
        $product = Product::factory()
            ->has(ProductImage::factory()->count(3), 'images')
            ->create(['country_id' => $country->id]);

        $response = $this->get(route('products.edit', $product));

        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Admin/Products/Edit')
                ->has('product')
                ->where('product.id', $product->id)
                ->where('product.country_id', $country->id)
                ->has('product.images', 3)
                ->etc()
                ->has('countries', Country::count())
        );

        $response->assertStatus(200);
    }

    public function test_admin_can_update_an_existing_product(): void
    {
        $admin = User::factory()->create();
        $this->actingAs($admin);

        $country = Country::factory()->create();
        $product = Product::factory()->create(['country_id' => $country->id]);

        $description = fake()->text();

        Storage::fake('public');
        Storage::makeDirectory('images/products/featured');
        $image = UploadedFile::fake()->image("product.jpg");

        $productData = [
            'name' => 'Test Product',
            'slug' => 'test-slug-for-product',
            'description' => $description,
            'price' => "895.00",
            'duration' => 8,
            'image' => $image,
            'country_id' => $country->id
        ];

        $response = $this->post(route('products.update', $product), $productData);
        $response->assertRedirect(route('products.index'));

        $this->assertDatabaseHas('products', Arr::except($productData, 'image'));

        $product = Product::first();

        Storage::disk('public')->assertExists($product->getRawOriginal('image'));
    }
}
