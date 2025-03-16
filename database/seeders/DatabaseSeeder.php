<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Country::factory(10)->create();

        User::factory()->create([
            'name' => 'Tester',
            'email' => 'test@mail.com',
        ]);

        Product::factory(2)->create()->each(function ($product) {
            ProductImage::factory(rand(3, 5))->create([
                'product_id' => $product->id,
            ]);
        });

    }
}
