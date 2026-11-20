<?php

namespace Tests\Feature;

use App\Enums\BlogPost\Status;
use App\Enums\Trip\TripFormat;
use App\Enums\Trip\TripType;
use App\Models\BlogPost;
use App\Models\Destination;
use App\Models\Itinerary;
use App\Models\Trip;
use App\Models\User;
use Database\Seeders\CountrySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class ImageAltTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(CountrySeeder::class);
        $this->actingAs(User::factory()->admin()->create());

        Storage::fake(config('images.disk'));
        Storage::makeDirectory(config('images.directory'));
    }

    // Story hero image

    public function test_new_story_saves_the_alt_of_its_hero_image(): void
    {
        $this->post(route('admin.posts.store'), $this->storyData([
            'featured_image' => UploadedFile::fake()->image('hero.jpg', 1600, 1000),
            'featured_image_alt' => 'Kastelen langs de Dordogne bij zonsondergang',
        ]))->assertRedirect(route('admin.posts.index'));

        $this->assertSame('Kastelen langs de Dordogne bij zonsondergang', BlogPost::firstOrFail()->heroImage->alt);
    }

    public function test_story_page_renders_the_alt_of_its_hero_image(): void
    {
        $post = $this->publishedStoryWithHero('Kastelen langs de Dordogne');

        $this->get(route('blog.show', $post))
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('post.hero_image.alt', 'Kastelen langs de Dordogne')
                // The Open Graph image describes the same photo
                ->where('seo.og_image_alt', 'Kastelen langs de Dordogne')
                ->etc()
            );
    }

    public function test_emptying_the_alt_falls_back_to_the_story_title(): void
    {
        $post = $this->publishedStoryWithHero('Kastelen langs de Dordogne');

        $this->put(route('admin.posts.update', $post), $this->storyData([
            'title' => $post->title,
            'status' => Status::Published->value,
            'featured_image' => $post->heroImage->public_url,
            'featured_image_alt' => '',
        ]));

        $this->assertNull($post->heroImage()->first()->alt);
        $this->get(route('blog.show', $post->fresh()))
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('post.hero_image.alt', null)
                ->where('seo.og_image_alt', $post->title)
                ->etc()
            );
    }

    public function test_an_alt_without_an_image_is_ignored(): void
    {
        // The form keeps an alt typed before the image was removed; without an image there is nothing to describe
        $this->post(route('admin.posts.store'), $this->storyData([
            'featured_image_alt' => 'Kastelen langs de Dordogne',
        ]))->assertSessionHasNoErrors()->assertRedirect(route('admin.posts.index'));

        $post = BlogPost::firstOrFail();
        $this->assertNull($post->heroImage);
        $this->assertDatabaseCount('images', 0);
    }

    public function test_an_alt_longer_than_255_characters_is_refused(): void
    {
        $this->post(route('admin.posts.store'), $this->storyData([
            'featured_image_alt' => str_repeat('a', 256),
        ]))->assertSessionHasErrors('featured_image_alt');
    }

    // Trip hero image

    public function test_new_trip_saves_the_alt_of_its_hero_image(): void
    {
        $this->post(route('admin.trips.store'), [
            'name' => 'Met de nachttrein naar Verona',
            'subtitle' => 'De stad die zich laag voor laag laat ontdekken',
            'description' => '<p>Een reis.</p>',
            'heroImage' => UploadedFile::fake()->image('hero.jpg', 1600, 1000),
            'hero_image_alt' => 'De Ponte Pietra over de Adige',
            'images' => [UploadedFile::fake()->image('img.jpg')],
            'destinations' => Destination::factory(1)->create()->modelKeys(),
            'published_at' => now()->toDateTimeString(),
            'type' => TripType::CityTrip->value,
            'format' => TripFormat::Individual->value,
        ])->assertSessionHasNoErrors();

        $trip = Trip::where('name', 'Met de nachttrein naar Verona')->firstOrFail();
        $this->assertSame('De Ponte Pietra over de Adige', $trip->heroImage->alt);

        // Only the hero image gets the alt, not the gallery
        $this->assertNull($trip->images()->first()->alt);
    }

    // Itinerary image

    public function test_itinerary_saves_the_alt_of_its_image(): void
    {
        $itinerary = Itinerary::factory()->for(Trip::factory())->create();

        $this->post(route('admin.itineraries.update', $itinerary), [
            'trip_id' => $itinerary->trip_id,
            'type' => $itinerary->type->value,
            'title' => 'Aankomst in Verona',
            'description' => 'Een dag in de stad.',
            'day_from' => 1,
            'image' => UploadedFile::fake()->image('verona.jpg'),
            'image_alt' => 'Het amfitheater van Verona',
        ])->assertSessionHasNoErrors()->assertRedirect();

        $this->assertSame('Het amfitheater van Verona', $itinerary->image()->first()->alt);
    }

    private function publishedStoryWithHero(string $alt): BlogPost
    {
        $this->post(route('admin.posts.store'), $this->storyData([
            'status' => Status::Published->value,
            'featured_image' => UploadedFile::fake()->image('hero.jpg', 1600, 1000),
            'featured_image_alt' => $alt,
        ]));

        return BlogPost::with('heroImage')->firstOrFail();
    }

    private function storyData(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Langs de Vézère',
            'body' => '<p>Het verhaal.</p>',
            'status' => Status::Draft->value,
        ], $overrides);
    }
}
