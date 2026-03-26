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
        NewsletterSubscriber::factory(250)->create();
        NewsletterCampaign::factory(5)->withHeroImage()->create();

        // Admin
        User::factory()->admin()->create();

        // Seed destinations first
        $this->call(CountrySeeder::class);
        $this->call(DestinationSeeder::class);
        $this->call(SettingsSeeder::class);

        // Trips
        $trips = Trip::factory(25)
            ->withHeroImage()
            ->withImages(10)
            ->withDestination()
            ->withAnItinerary()
            ->withItems()
            ->withPracticalInfo()
            ->withBlockedDates()
            ->withTransport()
            ->withPrices()
            ->create();

        Booking::factory(75)
            ->recycle($trips)
            ->withTravelers()
            ->create();
<<<<<<< HEAD

        // Blog posts
        $this->call(BlogPostSeeder::class);
=======
>>>>>>> b9e884b3fa401a1a668de43a24b0f92b9502b33e
    }
}
