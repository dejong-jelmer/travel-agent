<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Image;
use App\Models\Itinerary;
use App\Models\Product;
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
        $countries = Country::factory(10)->create();

        User::factory()->create([
            'name' => 'Tester',
            'email' => 'test@mail.com',
        ]);

        Product::factory(8)
            ->has(Image::factory()->count(3), 'images')
            ->create()->each(function ($product) use ($countries) {
                $product->images()->inRandomOrder()->first()->update(['featured' => true]);
                $product->countries()->attach(
                    $countries->random(rand(1, 3))->modelKeys()
                );
                for ($i = 1; $i <= $product->duration; $i++) {
                    Itinerary::factory()
                        ->has(Image::factory(), 'image')
                        ->create([
                            'product_id' => $product->id,
                            'order' => $i,
                        ]);
                }
            });
    }
}
