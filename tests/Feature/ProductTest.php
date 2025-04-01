<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\Product;
use App\Models\Image;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    private array $productData;

    private Collection $countries;

    private Product $product;

    const RELATIONS = 2;

    protected function setUp(): void
    {
        parent::setUp();
        $admin = User::factory()->create();
        $this->actingAs($admin);

        $this->countries = Country::factory(self::RELATIONS)->create();
        Storage::fake('public');
        Storage::makeDirectory('images');

        $this->productData = [
            'name' => fake()->words(2, true),
            'slug' => fake()->slug(),
            'description' => fake()->paragraph(),
            'price' => randomPrice(),
            'duration' => fake()->randomDigit(),
            'featuredImage' => UploadedFile::fake()->image('image.jpg'),
            'images' => [],
            'countries' => $this->countries->modelKeys(),
        ];
        for($i = 1; $i <= Self::RELATIONS; $i++) {
            $this->productData['images'][] = UploadedFile::fake()->image("image$i.jpg");
        }
    }

    public function test_admin_can_view_the_product_index_page(): void
    {
        $countries = $this->countries;
        $products = Product::factory()
            ->count(3)
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
                ->where('products.0.price', $products[0]->price)
                ->where('products.0.featured_image', $products[0]->featuredImage)
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

        $this->assertDatabaseHas('products', Arr::except($this->productData, ['featuredImage', 'images', 'countries']));
        $this->assertDatabaseHas('country_product', [
            'product_id' => $product->id,
            'country_id' => $this->countries->first()->id,
            'country_id' => $this->countries->last()->id,
        ]);

        $featuredImagePath = $this->productData['featuredImage']->getClientOriginalName();
        $this->assertDatabaseHas('images', [
            'imageable_id' => $product->id,
            'path' => $featuredImagePath,
            'featured' => true,
        ]);

        Storage::assertExists($featuredImagePath);
        $this->assertCount(self::RELATIONS, $product->images);

        foreach ($product->images as $index => $image) {
            $path = $this->productData['images'][$index]->getClientOriginalName();
            $this->assertDatabaseHas('images', [
                'imageable_id' => $product->id,
                'path' => $path,
            ]);
            Storage::assertExists($path);
        }
    }

    public function test_admin_can_show_singel_product(): void
    {
        $countries = Country::factory(self::RELATIONS)->create();
        $product = Product::factory()
            ->has(Image::factory(['featured' => true]), 'featuredImage')
            ->has(Image::factory()->count(self::RELATIONS), 'images')
            ->create();

        foreach($countries as $country) {
            $product->countries()->attach($country->id);
        }

        $response = $this->get(route('products.show', $product));
        $response->assertInertia(
            fn (AssertableInertia $page) => $page->component('Admin/Products/Show')
                ->has('product')
                ->where('product.id', $product->id)
                ->where('product.name', $product->name)
                ->where('product.slug', $product->slug)
                ->where('product.duration', $product->duration)
                ->where('product.price', $product->price)
                ->where('product.active', $product->active)
                ->where('product.featured', $product->featured)
                ->where('product.published_at', $product->published_at->format('Y-m-d H:i:s'))
                ->where('product.featured_image', $product->featuredImage)
                ->has('product.images', self::RELATIONS)
                ->has('product.countries', self::RELATIONS)
        );

        $response->assertStatus(200);
    }

    public function test_admin_can_show_the_product_edit_page(): void
    {
        $product = Product::factory()
            ->has(Country::factory()->count(self::RELATIONS), 'countries')
            ->has(Image::factory(['featured' => true]), 'featuredImage')
            ->has(Image::factory()->count(self::RELATIONS), 'images')
            ->create();
        $response = $this->get(route('products.edit', $product));

        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Admin/Products/Edit')
            ->has('product')
            ->where('product.id', $product->id)
            ->where('product.featured_image', $product->featuredImage)
            ->has('product.images', self::RELATIONS)
            ->has('product.countries', self::RELATIONS)
            ->etc()
        );

        $response->assertStatus(200);
    }

    public function test_admin_can_update_an_existing_product(): void
    {
        $countries = $this->countries;
        $product = Product::factory()->create();

        $response = $this->post(route('products.update', $product), $this->productData);
        $response->assertRedirect(route('products.show', $product));

        $this->assertDatabaseHas('products', Arr::except($this->productData, ['featuredImage', 'images', 'countries']));
        $this->assertDatabaseHas('country_product', [
            'product_id' => $product->id,
            'country_id' => $countries->first()->id,
            'country_id' => $countries->last()->id,
        ]);

        $featuredImagePath = $this->productData['featuredImage']->getClientOriginalName();
        Storage::assertExists($featuredImagePath);
        $this->assertDatabaseHas('images', [
            'imageable_id' => $product->id,
            'path' => $featuredImagePath,
        ]);

        $this->assertCount(self::RELATIONS, $product->images);

        foreach ($product->images as $index => $image) {
            $path = $this->productData['images'][$index]->getClientOriginalName();
            $this->assertDatabaseHas('images', [
                'imageable_id' => $product->id,
                'path' => $path,
            ]);

            Storage::assertExists($path);
        }
    }

    public function test_admin_can_destroy_a_product(): void
    {
        $product = Product::factory()->create();

        $featuredImage = Image::factory()->create([
            'imageable_id' => $product->id,
            'imageable_type' => Product::class,
            'featured' => true,
        ]);

        $images = Image::factory(self::RELATIONS)->create([
            'imageable_id' => $product->id,
            'imageable_type' => Product::class,
        ]);

         $response = $this->delete(route('products.destroy', $product));

         $response->assertRedirect(route('products.index'));

         $this->assertSoftDeleted($product);
         $this->assertSoftDeleted($featuredImage);
         foreach($images as $image) {
             $this->assertSoftDeleted($image);
         }
    }
}
