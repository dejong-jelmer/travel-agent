<?php

namespace Tests\Feature;

use App\Enums\TestimonialRelationship;
use App\Models\Image;
use App\Models\Testimonial;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class TestimonialTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->admin()->create());

        Storage::fake(config('images.disk'));
        Storage::makeDirectory(config('images.directory'));
    }

    /**
     * @return array<string, mixed>
     */
    private function validData(array $overrides = []): array
    {
        return [
            'trip_id' => null,
            'quote' => 'De nachttrein was het begin van de vakantie, niet de weg ernaartoe.',
            'body' => null,
            'author_name' => 'Henk',
            'relationship' => TestimonialRelationship::Customer->value,
            'author_context' => null,
            'travelled_on' => null,
            'link_url' => null,
            'link_label' => null,
            'is_published' => true,
            ...$overrides,
        ];
    }

    // Admin index

    public function test_admin_can_view_the_testimonials_in_their_order(): void
    {
        $trip = Trip::factory()->create();
        $second = Testimonial::factory()->for($trip)->create(['sort_order' => 2]);
        $first = Testimonial::factory()->personal()->create(['sort_order' => 1]);

        $response = $this->get(route('admin.testimonials.index'));

        $response->assertOk()->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Admin/Testimonials/Index')
                ->has('testimonials', 2)
                ->where('testimonials.0.id', $first->id)
                ->where('testimonials.0.trip_name', null)
                ->where('testimonials.0.relationship', TestimonialRelationship::Personal->label())
                ->where('testimonials.1.id', $second->id)
                ->where('testimonials.1.trip_name', $trip->name)
        );
    }

    // Admin create

    public function test_admin_can_view_testimonial_create(): void
    {
        $trip = Trip::factory()->create();

        $response = $this->get(route('admin.testimonials.create'));

        $response->assertOk()->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Admin/Testimonials/Create')
                ->where('tripOptions', [['id' => (string) $trip->id, 'name' => $trip->name]])
                ->where('relationshipOptions', TestimonialRelationship::options())
                ->where('limits', ['quote' => 300, 'body' => 1500, 'author_context' => 120])
        );
    }

    public function test_admin_can_create_a_testimonial_at_the_end_of_the_order(): void
    {
        $trip = Trip::factory()->create();
        Testimonial::factory()->create(['sort_order' => 4]);

        $response = $this->post(route('admin.testimonials.store'), $this->validData([
            'trip_id' => $trip->id,
            'body' => "Eerste alinea.\n\nTweede alinea.",
            'relationship' => TestimonialRelationship::Personal->value,
            'author_context' => 'Mijn vader, over zijn reis naar de Vézère-vallei',
            'travelled_on' => '2024-05-01',
            'link_url' => 'https://omdatwereizen.nl/mijn-verhalen/vezere',
            'link_label' => 'Lees zijn verhaal',
        ]));

        $response->assertSessionHasNoErrors()->assertRedirect(route('admin.testimonials.index'));

        $testimonial = Testimonial::latest('id')->firstOrFail();
        $this->assertSame($trip->id, $testimonial->trip_id);
        $this->assertSame(TestimonialRelationship::Personal, $testimonial->relationship);
        $this->assertSame('Mijn vader, over zijn reis naar de Vézère-vallei', $testimonial->author_context);
        $this->assertSame('2024-05-01', $testimonial->travelled_on->toDateString());
        $this->assertSame('Lees zijn verhaal', $testimonial->link_label);
        $this->assertTrue($testimonial->is_published);
        $this->assertSame(5, $testimonial->sort_order);
    }

    public function test_admin_can_create_a_general_testimonial(): void
    {
        $this->post(route('admin.testimonials.store'), $this->validData())->assertSessionHasNoErrors();

        $this->assertNull(Testimonial::firstOrFail()->trip_id);
    }

    public function test_a_new_testimonial_is_not_published_unless_asked(): void
    {
        $data = $this->validData();
        unset($data['is_published']);

        $this->post(route('admin.testimonials.store'), $data)->assertSessionHasNoErrors();

        $this->assertFalse(Testimonial::firstOrFail()->is_published);
    }

    public function test_admin_can_create_a_testimonial_with_a_photo(): void
    {
        $this->post(route('admin.testimonials.store'), $this->validData([
            'photo' => UploadedFile::fake()->image('henk.jpg'),
        ]))->assertSessionHasNoErrors();

        $image = Testimonial::firstOrFail()->image;
        $this->assertNotNull($image);
        Storage::disk(config('images.disk'))->assertExists($image->full_path);
    }

    // Admin edit

    public function test_admin_can_view_testimonial_edit(): void
    {
        $testimonial = Testimonial::factory()->create(['travelled_on' => '2024-05-01']);

        $response = $this->get(route('admin.testimonials.edit', $testimonial));

        $response->assertOk()->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Admin/Testimonials/Edit')
                ->where('testimonial.id', $testimonial->id)
                // Without a time, as the month picker reads it
                ->where('testimonial.travelled_on', '2024-05-01')
                ->has('tripOptions')
                ->has('relationshipOptions')
                ->has('limits')
        );
    }

    public function test_admin_can_update_a_testimonial(): void
    {
        $testimonial = Testimonial::factory()->create(['sort_order' => 3]);

        $response = $this->put(route('admin.testimonials.update', $testimonial), $this->validData([
            'quote' => 'Een nieuw citaat.',
            'author_name' => 'Ria',
            'is_published' => false,
        ]));

        $response->assertSessionHasNoErrors()->assertRedirect(route('admin.testimonials.index'));

        $testimonial->refresh();
        $this->assertSame('Een nieuw citaat.', $testimonial->quote);
        $this->assertSame('Ria', $testimonial->author_name);
        $this->assertFalse($testimonial->is_published);
        $this->assertSame(3, $testimonial->sort_order);
    }

    public function test_admin_can_keep_and_remove_the_photo(): void
    {
        $this->post(route('admin.testimonials.store'), $this->validData([
            'photo' => UploadedFile::fake()->image('henk.jpg'),
        ]));
        $testimonial = Testimonial::firstOrFail();
        $image = $testimonial->image;

        // The edit form sends the URL of the photo it already has
        $this->put(route('admin.testimonials.update', $testimonial), $this->validData([
            'photo' => $image->public_url,
        ]))->assertSessionHasNoErrors();
        $this->assertTrue($testimonial->image()->first()->is($image));

        $this->put(route('admin.testimonials.update', $testimonial), $this->validData([
            'photo' => null,
        ]))->assertSessionHasNoErrors();
        $this->assertNull($testimonial->image()->first());
    }

    // Admin order and publication

    public function test_admin_can_update_the_order(): void
    {
        $first = Testimonial::factory()->create(['sort_order' => 1]);
        $second = Testimonial::factory()->create(['sort_order' => 2]);

        $response = $this->patch(route('admin.testimonials.order'), [
            'testimonials' => [
                ['id' => $first->id, 'sort_order' => 2],
                ['id' => $second->id, 'sort_order' => 1],
            ],
        ]);

        $response->assertOk()->assertJson(['success' => true]);
        $this->assertSame(2, $first->refresh()->sort_order);
        $this->assertSame(1, $second->refresh()->sort_order);
    }

    public function test_admin_can_publish_and_unpublish_from_the_list(): void
    {
        $testimonial = Testimonial::factory()->create();

        $this->from(route('admin.testimonials.index'))
            ->patch(route('admin.testimonials.publish', $testimonial))
            ->assertRedirect(route('admin.testimonials.index'));
        $this->assertTrue($testimonial->refresh()->is_published);

        $this->patch(route('admin.testimonials.publish', $testimonial));
        $this->assertFalse($testimonial->refresh()->is_published);
    }

    // Admin delete

    public function test_admin_can_delete_a_testimonial_with_its_photo(): void
    {
        $this->post(route('admin.testimonials.store'), $this->validData([
            'photo' => UploadedFile::fake()->image('henk.jpg'),
        ]));
        $testimonial = Testimonial::firstOrFail();
        $path = $testimonial->image->full_path;

        $response = $this->delete(route('admin.testimonials.destroy', $testimonial));

        $response->assertRedirect(route('admin.testimonials.index'));
        $this->assertModelMissing($testimonial);
        $this->assertSame(0, Image::withTrashed()->count());
        Storage::disk(config('images.disk'))->assertMissing($path);
    }

    // Validation

    public function test_the_quote_is_required(): void
    {
        $this->post(route('admin.testimonials.store'), $this->validData(['quote' => '']))
            ->assertSessionHasErrors('quote');
    }

    public function test_the_quote_can_be_at_most_300_characters(): void
    {
        $this->post(route('admin.testimonials.store'), $this->validData(['quote' => str_repeat('a', 301)]))
            ->assertSessionHasErrors('quote');
        $this->assertDatabaseCount('testimonials', 0);

        $this->post(route('admin.testimonials.store'), $this->validData(['quote' => str_repeat('a', 300)]))
            ->assertSessionHasNoErrors();
    }

    public function test_the_body_can_be_at_most_1500_characters(): void
    {
        $this->post(route('admin.testimonials.store'), $this->validData(['body' => str_repeat('a', 1501)]))
            ->assertSessionHasErrors('body');
        $this->assertDatabaseCount('testimonials', 0);

        $this->post(route('admin.testimonials.store'), $this->validData(['body' => str_repeat('a', 1500)]))
            ->assertSessionHasNoErrors();
    }

    public function test_the_author_context_is_required_for_a_personal_testimonial(): void
    {
        $this->post(route('admin.testimonials.store'), $this->validData([
            'relationship' => TestimonialRelationship::Personal->value,
            'author_context' => null,
        ]))->assertSessionHasErrors(['author_context' => __('testimonial.validation.author_context_required')]);
        $this->assertDatabaseCount('testimonials', 0);

        $this->post(route('admin.testimonials.store'), $this->validData([
            'relationship' => TestimonialRelationship::Personal->value,
            'author_context' => 'Mijn vader, over zijn reis naar de Vézère-vallei',
        ]))->assertSessionHasNoErrors();
    }

    public function test_the_author_context_is_optional_for_a_customer(): void
    {
        $this->post(route('admin.testimonials.store'), $this->validData([
            'relationship' => TestimonialRelationship::Customer->value,
            'author_context' => null,
        ]))->assertSessionHasNoErrors();
    }

    public function test_the_author_context_can_be_at_most_120_characters(): void
    {
        $this->post(route('admin.testimonials.store'), $this->validData(['author_context' => str_repeat('a', 121)]))
            ->assertSessionHasErrors('author_context');
    }

    public static function invalidRelationships(): array
    {
        return [
            'missing' => [null],
            'empty' => [''],
            'unknown' => ['colleague'],
        ];
    }

    #[DataProvider('invalidRelationships')]
    public function test_the_relationship_must_be_valid(?string $relationship): void
    {
        $this->post(route('admin.testimonials.store'), $this->validData(['relationship' => $relationship]))
            ->assertSessionHasErrors('relationship');
        $this->assertDatabaseCount('testimonials', 0);
    }

    #[DataProvider('invalidRelationships')]
    public function test_the_relationship_must_stay_valid_on_update(?string $relationship): void
    {
        $testimonial = Testimonial::factory()->create();

        $this->put(route('admin.testimonials.update', $testimonial), $this->validData(['relationship' => $relationship]))
            ->assertSessionHasErrors('relationship');
        $this->assertSame(TestimonialRelationship::Customer, $testimonial->refresh()->relationship);
    }

    public static function invalidLinks(): array
    {
        return [
            'script' => ['javascript:alert(1)'],
            'no scheme' => ['omdatwereizen.nl/mijn-verhalen'],
            'other scheme' => ['ftp://omdatwereizen.nl/verhaal'],
        ];
    }

    #[DataProvider('invalidLinks')]
    public function test_the_link_must_be_a_web_address(string $url): void
    {
        $this->post(route('admin.testimonials.store'), $this->validData(['link_url' => $url]))
            ->assertSessionHasErrors('link_url');
    }

    public function test_the_trip_must_exist(): void
    {
        $this->post(route('admin.testimonials.store'), $this->validData(['trip_id' => 999]))
            ->assertSessionHasErrors('trip_id');
    }
}
