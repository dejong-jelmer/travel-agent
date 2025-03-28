<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_displays_products()
    {
        $country = Country::factory()->create();
        $product = Product::factory()->create();
        $product->countries()->attach($country->id);

        $response = $this->get(route('home'));

        $response->assertInertia(fn (AssertableInertia $page) => $page->component('Home')
            ->has('products', 1)
            ->where('products.0.id', $product->id)
            ->where('products.0.price', $product->price)
        );
        $this->assertDatabaseHas('country_product', [
            'product_id' => $product->id,
            'country_id' => $country->id,
        ]);

        $response->assertStatus(200);
    }

    public function test_about_page_returns_200()
    {
        $this->get(route('about'))->assertStatus(200);
    }

    public function test_contact_page_returns_200()
    {
        $this->get(route('contact'))->assertStatus(200);
    }

    public function test_product_page_displays_correct_product()
    {
        $country = Country::factory()->create();
        $product = Product::factory()->create();
        $product->countries()->attach($country->id);

        $response = $this->get(route('trip.show', $product));

        $response->assertInertia(fn (AssertableInertia $page) => $page->component('Products/Show')
            ->has('product')
            ->where('product.id', $product->id)
            ->where('product.name', $product->name)
            ->where('product.slug', $product->slug)
            ->where('product.duration', $product->duration)
            ->where('product.price', $product->price)
            ->where('product.active', $product->active)
            ->where('product.featured', $product->featured)
            ->where('product.published_at', $product->published_at->format('Y-m-d H:i:s'))
        );

        $this->assertDatabaseHas('country_product', [
            'product_id' => $product->id,
            'country_id' => $country->id,
        ]);
        $response->assertStatus(200);
    }
}
