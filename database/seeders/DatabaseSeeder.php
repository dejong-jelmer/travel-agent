<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Country;
use App\Models\Image;
use App\Models\Itinerary;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Remove old images
        $disk = Storage::disk(config('images.disk'));
        $dir = config('images.directory');
        $files = $disk->allFiles($dir);
        if (! empty($files)) {
            $disk->delete($files);
        }

        $countries = Country::factory(10)->create();

        User::factory()->create([
            'name' => 'Tester',
            'email' => 'test@mail.com',
        ]);

        Trip::factory(8)
            ->has(Booking::factory(), 'bookings')
            ->has(Image::factory()->count(3), 'images')
            ->create()->each(function ($trip) use ($countries) {
                $trip->images()->inRandomOrder()->first()->update(['is_primary' => true]);
                $trip->countries()->attach(
                    $countries->random(rand(1, 3))->modelKeys()
                );
                for ($i = 1; $i <= $trip->duration; $i++) {
                    Itinerary::factory()
                        ->has(Image::factory(), 'image')
                        ->create([
                            'trip_id' => $trip->id,
                            'order' => $i,
                        ]);
                }
            });
    }
}
