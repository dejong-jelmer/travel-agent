<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\NewsletterCampaign;
use App\Models\NewsletterSubscriber;
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

        // Newsletters
        NewsletterSubscriber::factory(1500)->create();
        NewsletterCampaign::factory(150)->withHeroImage()->create();

        // Admin
        User::factory()->admin()->create();

        // Trips
        Trip::factory(50)
            ->withHeroImage()
            ->withImages(10)
            ->withCountry()
            ->withAnItinerary()
            ->create()->each(
                fn ($trip) => Booking::factory(fake()->numberBetween(0, 5))
                    ->for($trip, 'trip')
                    ->create()
            );
    }
}
