<?php

namespace Tests\Feature;

use App\Models\Testimonial;
use App\Models\Trip;
use Database\Seeders\CountrySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class TestimonialDisplayTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CountrySeeder::class);
    }

    public function test_only_the_published_testimonials_of_the_trip_are_passed_in_their_order(): void
    {
        $trip = Trip::factory()->create();
        $second = Testimonial::factory()->published()->for($trip)->create(['sort_order' => 5]);
        $first = Testimonial::factory()->published()->for($trip)->create(['sort_order' => 2]);
        Testimonial::factory()->for($trip)->create(['sort_order' => 1]);
        Testimonial::factory()->published()->for(Trip::factory())->create(['sort_order' => 1]);
        Testimonial::factory()->published()->create(['sort_order' => 1]);

        $this->get(route('trips.show', $trip))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Trip/Show')
                ->has('testimonials', 2)
                ->where('testimonials.0.id', $first->id)
                ->where('testimonials.1.id', $second->id)
            );
    }

    public function test_a_testimonial_is_passed_ready_for_display(): void
    {
        $trip = Trip::factory()->create();
        $testimonial = Testimonial::factory()->published()->personal()->for($trip)->create([
            'quote' => 'De grotten waren ouder dan alles wat ik ooit zag.',
            'author_name' => 'Henk',
            'travelled_on' => '2024-05-01',
            'link_url' => 'https://omdatwereizen.nl/mijn-verhalen/vezere',
            'link_label' => 'Lees zijn verhaal',
        ]);
        $photo = $testimonial->image()->create([
            'path' => 'henk.jpg',
            'original_name' => 'henk.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 1000,
        ]);

        $this->get(route('trips.show', $trip))
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('testimonials.0.quote', 'De grotten waren ouder dan alles wat ik ooit zag.')
                ->where('testimonials.0.author_name', 'Henk')
                ->where('testimonials.0.author_context', 'Mijn vader, over zijn reis naar de Vézère-vallei')
                ->where('testimonials.0.travelled_in', 'mei 2024')
                ->where('testimonials.0.photo.id', $photo->id)
                ->has('testimonials.0.photo.sources')
                ->where('testimonials.0.link_url', 'https://omdatwereizen.nl/mijn-verhalen/vezere')
                ->where('testimonials.0.link_label', 'Lees zijn verhaal')
            );
    }

    public function test_the_body_is_passed_as_paragraphs_when_filled(): void
    {
        $trip = Trip::factory()->create();
        // As a textarea sends it: CRLF line endings, a blank line between paragraphs and a hard-wrapped line
        Testimonial::factory()->published()->for($trip)->create([
            'body' => "Eerste alinea,\r\nover twee regels.\r\n\r\n\r\nTweede alinea.\r\n  \r\nDerde alinea.\r\n",
        ]);

        $this->get(route('trips.show', $trip))
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('testimonials.0.paragraphs', [
                    'Eerste alinea, over twee regels.',
                    'Tweede alinea.',
                    'Derde alinea.',
                ])
            );
    }

    public function test_a_testimonial_without_a_body_has_no_paragraphs(): void
    {
        $trip = Trip::factory()->create();
        Testimonial::factory()->published()->for($trip)->create(['body' => null]);

        $this->get(route('trips.show', $trip))
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('testimonials.0.paragraphs', [])
                ->where('testimonials.0.travelled_in', null)
            );
    }

    public function test_a_trip_without_published_testimonials_passes_an_empty_list(): void
    {
        $trip = Trip::factory()->create();
        Testimonial::factory()->for($trip)->create();

        $this->get(route('trips.show', $trip))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('testimonials', [])
            );
    }

    public function test_the_trip_page_has_no_review_or_rating_structured_data(): void
    {
        $trip = Trip::factory()->create();
        Testimonial::factory()->published()->for($trip)->create();
        Testimonial::factory()->published()->personal()->for($trip)->create();

        $this->assertNoReviewStructuredData($this->get(route('trips.show', $trip)), 'TouristTrip');
    }

    // Homepage

    public function test_only_the_published_general_testimonials_are_passed_to_the_homepage_in_their_order(): void
    {
        $second = Testimonial::factory()->published()->create(['sort_order' => 5]);
        $first = Testimonial::factory()->published()->personal()->create(['sort_order' => 2]);
        Testimonial::factory()->create(['sort_order' => 1]);
        Testimonial::factory()->published()->for(Trip::factory())->create(['sort_order' => 1]);

        $this->get(route('home'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Home')
                ->has('testimonials', 2)
                ->where('testimonials.0.id', $first->id)
                ->where('testimonials.0.author_context', 'Mijn vader, over zijn reis naar de Vézère-vallei')
                ->where('testimonials.1.id', $second->id)
            );
    }

    public function test_a_general_testimonial_is_passed_to_the_homepage_ready_for_display(): void
    {
        Testimonial::factory()->published()->create([
            'body' => "Eerste alinea.\r\n\r\nTweede alinea.",
            'travelled_on' => '2022-03-01',
        ]);

        $this->get(route('home'))
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('testimonials.0.paragraphs', ['Eerste alinea.', 'Tweede alinea.'])
                ->where('testimonials.0.travelled_in', 'maart 2022')
            );
    }

    public function test_the_homepage_passes_an_empty_list_without_published_general_testimonials(): void
    {
        Testimonial::factory()->create();
        Testimonial::factory()->published()->for(Trip::factory())->create();

        $this->get(route('home'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('testimonials', [])
            );
    }

    public function test_the_homepage_has_no_review_or_rating_structured_data(): void
    {
        Testimonial::factory()->published()->create();
        Testimonial::factory()->published()->personal()->create();

        $this->assertNoReviewStructuredData($this->get(route('home')), 'TravelAgency');
    }

    /**
     * Assert that the JSON-LD of the page describes the given type, but no review or rating, at any depth, and that
     * no rating terms appear anywhere else in the page.
     */
    private function assertNoReviewStructuredData(TestResponse $response, string $pageType): void
    {
        $response->assertOk();

        preg_match_all('#<script type="application/ld\+json">\s*(.+?)\s*</script>#s', $response->getContent(), $matches);
        $this->assertNotEmpty($matches[1], 'The page renders no JSON-LD block.');

        foreach ($matches[1] as $block) {
            $keys = [];
            $types = [];
            $this->collectKeysAndTypes(json_decode($block, true, flags: JSON_THROW_ON_ERROR), $keys, $types);

            $this->assertContains($pageType, $types);
            $this->assertEmpty(array_intersect($types, ['Review', 'AggregateRating', 'Rating']));
            $this->assertEmpty(array_intersect($keys, ['review', 'reviews', 'reviewRating', 'aggregateRating', 'ratingValue']));
        }

        $response->assertDontSee('AggregateRating')->assertDontSee('reviewRating')->assertDontSee('ratingValue');
    }

    /**
     * Collect every key and every @type at any depth of a decoded JSON-LD schema.
     *
     * @param  array<mixed>  $schema
     * @param  list<string|int>  $keys
     * @param  list<mixed>  $types
     */
    private function collectKeysAndTypes(array $schema, array &$keys, array &$types): void
    {
        foreach ($schema as $key => $value) {
            $keys[] = $key;
            if ($key === '@type') {
                $types = [...$types, ...(array) $value];
            }
            if (is_array($value)) {
                $this->collectKeysAndTypes($value, $keys, $types);
            }
        }
    }
}
