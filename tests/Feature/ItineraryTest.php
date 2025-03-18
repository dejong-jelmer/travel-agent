<?php

namespace Tests\Feature;

use App\Models\Itinerary;
use Inertia\Testing\AssertableInertia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Product;
use Tests\TestCase;
use App\Models\User;

class ItineraryTest extends TestCase
{
    use RefreshDatabase;
    public function test_product_itineraries_shows_product_itinerary(): void
    {
        $admin = User::factory()->create();
        $this->actingAs($admin);

        $product = Product::factory(1)->create()
            ->each(function ($product) {
                Itinerary::factory()
                    ->count($product->duration)
                    ->create(['product_id' => $product->id]);
            });

        $product = $product->first();
        $response = $this->get(route('products.itineraries.index', $product));

        $response->assertInertia(
            fn(AssertableInertia $page) =>
            $page->component('Admin/Products/Itineraries/Index')
                ->has('product')
                ->has('product.itineraries', $product->itineraries->count())
        );

        $response->assertStatus(200);
    }
}
