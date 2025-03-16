<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use App\Models\Itinerary;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $countries = Country::factory(10)->create();

        User::factory()->create([
            'name' => 'Tester',
            'email' => 'test@mail.com',
        ]);

        Product::factory(8)->create()->each(function ($product) use ($countries) {
            $product->countries()->attach(
                    $countries->random(rand(1, 3))
                        ->pluck('id')
                        ->toArray()
            );
            ProductImage::factory(rand(3, 5))->create([
                'product_id' => $product->id,
            ]);

            Itinerary::factory($product->duration)->create([
                'product_id' => $product->id,
            ]);
        });
    }
}
