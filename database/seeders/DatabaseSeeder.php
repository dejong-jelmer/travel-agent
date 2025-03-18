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

        $order = 0;
        Product::factory(8)->create()->each(function ($product) use ($countries, &$order) {
            $product->countries()->attach(
                    $countries->random(rand(1, 3))
                        ->pluck('id')
                        ->toArray()
            );
            ProductImage::factory(rand(3, 5))->create([
                'product_id' => $product->id,
            ]);
            for ($i = 1; $i <= $product->duration; $i++) {
                Itinerary::factory()->create([
                    'product_id' => $product->id,
                    'order' => $i,
                ]);
            }
        });
    }
}
