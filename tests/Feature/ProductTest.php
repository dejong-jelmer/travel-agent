<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;
use Illuminate\Database\Eloquent\Collection;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    private array $productData;
    private Collection $countries;
    private Product $product;

    public function setUp(): void
    {
        parent::setUp();
        $admin = User::factory()->create();
        $this->actingAs($admin);

        $this->countries = Country::factory(2)->create();
        Storage::fake('public');
        Storage::makeDirectory(config('product.featured-image-path'));

        $this->productData = [
            'name' => fake()->words(2, true),
            'slug' => fake()->slug(),
            'description' => fake()->paragraph(),
            'price' => (string) fake()->randomFloat(2, 300, 5000),
            'duration' => fake()->randomDigit(),
            'image' => UploadedFile::fake()->image('image.jpg'),
            'images' => [
                UploadedFile::fake()->image('image1.jpg'),
                UploadedFile::fake()->image('image2.jpg'),
            ],
            'countries' => $this->countries->modelKeys(),
        ];
    }

    public function test_admin_can_view_the_product_index_page(): void
    {
        $countries = $this->countries;
        $products = Product::factory()
            ->count(3)
            ->has(ProductImage::factory()->count(2), 'images')
            ->create()->each(function ($product) use ($countries) {
                $product->countries()->attach([
                    $countries->first()->id,
                    $countries->last()->id,
                ]);
            });

        $response = $this->get(route('products.index'));

        $response->assertInertia(
            fn (AssertableInertia $page) => $page->component('Admin/Products/Index')
                ->has('products', 3)
                ->where('products.0.id', $products[0]->id)
                ->where('products.0.price', price($products[0]->price))
                ->has('products.0.images', 2)
                ->where('products.0.images.0.id', $products[0]->images[0]->id)
        );

        foreach ($products as $product) {
            $this->assertDatabaseHas('country_product', [
                'product_id' => $product->id,
                'country_id' => $countries->first()->id,
                'country_id' => $countries->last()->id,
            ]);
        }

        $response->assertStatus(200);
    }

    public function test_admin_can_view_the_product_create_page(): void
    {
        $response = $this->get(route('products.create'));

        $response->assertInertia(
            fn (AssertableInertia $page) => $page->component('Admin/Products/Create')
        );
        $response->assertStatus(200);
    }

    public function test_admin_can_store_a_new_product(): void
    {
        $response = $this->post(route('products.store'), $this->productData);
        $product = Product::first();
        $response->assertRedirect(route('products.show', $product));

        $this->assertDatabaseHas('products', Arr::except($this->productData, ['image', 'images', 'countries']));
        $this->assertDatabaseHas('country_product', [
            'product_id' => $product->id,
            'country_id' => $this->countries->first()->id,
            'country_id' => $this->countries->last()->id,
        ]);
        $imagePath = config('product.featured-image-path') . "/{$this->productData['image']->getClientOriginalName()}";
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'image' => $imagePath,
        ]);

        Storage::disk('public')->assertExists($imagePath);
        $this->assertCount(2, $product->images);

        foreach ($product->images as $index => $image) {
            $path = config('product.images-path') ."/{$this->productData['images'][$index]->getClientOriginalName()}";
            $this->assertDatabaseHas('product_images', [
                'product_id' => $product->id,
                'path' => $path,
            ]);
            Storage::disk('public')->assertExists($path);
        }
    }

    public function test_admin_can_show_singel_product(): void
    {
        $country = Country::factory()->create();
        $product = Product::factory()
            ->has(ProductImage::factory()->count(3), 'images')
            ->create();
        $product->countries()->attach($country->id);

        $response = $this->get(route('products.show', $product));
        $response->assertInertia(
            fn (AssertableInertia $page) => $page->component('Admin/Products/Show')
                ->has('product')
                ->where('product.id', $product->id)
                ->where('product.name', $product->name)
                ->where('product.slug', $product->slug)
                ->where('product.duration', $product->duration)
                ->where('product.price', price($product->price))
                ->where('product.active', $product->active)
                ->where('product.featured', $product->featured)
                ->where('product.published_at', $product->published_at->format('Y-m-d H:i:s'))
                ->has('product.images', 3)
                ->has('product.countries', 1)
        );

        $response->assertStatus(200);
    }

    public function test_admin_can_show_the_product_edit_page(): void
    {
        $product = Product::factory()
            ->has(Country::factory()->count(2), 'countries')
            ->has(ProductImage::factory()->count(3), 'images')
            ->create();
        $response = $this->get(route('products.edit', $product));

        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Admin/Products/Edit')
            ->has('product')
            ->where('product.id', $product->id)
            ->has('product.images', 3)
            ->has('product.countries', 2)
            ->etc()
        );

        $response->assertStatus(200);
    }

    public function test_admin_can_update_an_existing_product(): void
    {
        $countries = $this->countries;
        $product = Product::factory()->create();

        Storage::fake('public');
        Storage::makeDirectory(config('product.featured-image-path'));

        $response = $this->post(route('products.update', $product), $this->productData);
        $response->assertRedirect(route('products.show', $product));

        $this->assertDatabaseHas('products', Arr::except($this->productData, ['image', 'images', 'countries']));
        $this->assertDatabaseHas('country_product', [
            'product_id' => $product->id,
            'country_id' => $countries->first()->id,
            'country_id' => $countries->last()->id,
        ]);

        $imagePath = config('product.featured-image-path') . "/{$this->productData['image']->getClientOriginalName()}";
        Storage::disk('public')->assertExists($imagePath);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'image' => $imagePath,
        ]);

        $this->assertCount(2, $product->images);

        foreach ($product->images as $index => $image) {
            $path = config('product.images-path') ."/{$this->productData['images'][$index]->getClientOriginalName()}";
            $this->assertDatabaseHas('product_images', [
                'product_id' => $product->id,
                'path' => $path,
            ]);

            Storage::disk('public')->assertExists($path);
        }
    }
}
