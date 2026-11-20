<?php

namespace Tests\Feature;

use App\Enums\BlogPost\Status;
use App\Models\BlogPost;
use App\Models\Trip;
use App\Models\User;
use Database\Seeders\CountrySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class BlogPostTripTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CountrySeeder::class);
    }

    // Admin

    public function test_admin_can_relate_trips_to_a_new_story(): void
    {
        $trips = Trip::factory()->count(2)->create();

        $this->actingAs(User::factory()->create())
            ->post(route('admin.posts.store'), $this->storyData(['trips' => $trips->pluck('id')->map(fn ($id) => (string) $id)->all()]))
            ->assertRedirect(route('admin.posts.index'));

        $this->assertEqualsCanonicalizing($trips->pluck('id')->all(), BlogPost::firstOrFail()->trips()->pluck('trips.id')->all());
    }

    public function test_admin_can_change_the_trips_of_a_story(): void
    {
        [$kept, $dropped, $added] = Trip::factory()->count(3)->create();
        $post = BlogPost::factory()->create();
        $post->trips()->attach([$kept->id, $dropped->id]);

        $this->actingAs(User::factory()->create())
            ->put(route('admin.posts.update', $post), $this->storyData(['trips' => [$kept->id, $added->id]]))
            // The slug follows the new title
            ->assertRedirect(route('admin.posts.show', $post->fresh()));

        $this->assertEqualsCanonicalizing([$kept->id, $added->id], $post->trips()->pluck('trips.id')->all());
    }

    public function test_saving_a_story_without_trips_removes_its_trips(): void
    {
        // The form data of the admin form leaves an empty list out
        $post = BlogPost::factory()->create();
        $post->trips()->attach(Trip::factory()->create());

        $this->actingAs(User::factory()->create())
            ->put(route('admin.posts.update', $post), $this->storyData());

        $this->assertSame(0, $post->trips()->count());
    }

    public function test_a_story_cannot_be_related_to_a_trip_that_does_not_exist(): void
    {
        $trip = Trip::factory()->create();
        $trip->delete();

        $this->actingAs(User::factory()->create())
            ->post(route('admin.posts.store'), $this->storyData(['trips' => [$trip->id, 999999]]))
            ->assertSessionHasErrors(['trips.0', 'trips.1']);

        $this->assertSame(0, BlogPost::count());
    }

    public function test_edit_page_selects_the_trips_of_the_story(): void
    {
        $trip = Trip::factory()->create(['name' => 'Bordeaux']);
        $post = BlogPost::factory()->create();
        $post->trips()->attach($trip);

        $this->actingAs(User::factory()->create())
            ->get(route('admin.posts.edit', $post))
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Admin/BlogPosts/Edit')
                ->where('tripIds', [(string) $trip->id])
                ->where('tripOptions', [['id' => (string) $trip->id, 'name' => 'Bordeaux']])
                ->etc()
            );
    }

    // Story page

    public function test_story_page_shows_its_published_trips(): void
    {
        $published = Trip::factory()->create();
        $unpublished = Trip::factory()->create(['published_at' => now()->addMonth()]);
        $deleted = Trip::factory()->create();
        $post = BlogPost::factory()->published()->create();
        $post->trips()->attach([$published->id, $unpublished->id, $deleted->id]);
        $deleted->delete();

        $this->get(route('blog.show', $post))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Blog/Show')
                ->has('trips', 1)
                ->where('trips.0.id', $published->id)
                ->has('trips.0.travel_mode')
                ->missing('trips.0.pivot')
                ->etc()
            );
    }

    public function test_story_page_without_trips_shares_an_empty_list(): void
    {
        $post = BlogPost::factory()->published()->create();

        $this->get(route('blog.show', $post))
            ->assertInertia(fn (AssertableInertia $page) => $page->where('trips', [])->etc());
    }

    // Trip page

    public function test_trip_page_shows_its_published_stories_newest_first(): void
    {
        $trip = Trip::factory()->create();
        $older = BlogPost::factory()->published()->create(['published_at' => now()->subWeek()]);
        $newer = BlogPost::factory()->published()->create(['published_at' => now()->subDay()]);
        $draft = BlogPost::factory()->draft()->create();
        $trip->blogPosts()->attach([$older->id, $newer->id, $draft->id]);

        $this->get(route('trips.show', $trip))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Trip/Show')
                ->has('stories', 2)
                ->where('stories.0.slug', $newer->slug)
                ->where('stories.1.slug', $older->slug)
                // A card has no use for the body
                ->missing('stories.0.body')
                ->etc()
            );
    }

    public function test_trip_page_without_stories_shares_an_empty_list(): void
    {
        $trip = Trip::factory()->create();

        $this->get(route('trips.show', $trip))
            ->assertInertia(fn (AssertableInertia $page) => $page->where('stories', [])->etc());
    }

    private function storyData(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Met de nachttrein naar Wenen',
            'body' => '<p>Het verhaal.</p>',
            'status' => Status::Draft->value,
        ], $overrides);
    }
}
