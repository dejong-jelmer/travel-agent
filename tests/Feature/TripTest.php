<?php

namespace Tests\Feature;

use App\Enums\Transport;
use App\Enums\Trip\ItemType;
use App\Enums\Trip\KeyFactIcon;
use App\Enums\Trip\PracticalInfo;
use App\Enums\Trip\PriceLabel;
use App\Enums\UserRole;
use App\Models\Destination;
use App\Models\Image;
use App\Models\Itinerary;
use App\Models\Trip;
use App\Models\TripItem;
use App\Models\User;
use Database\Seeders\CountrySeeder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class TripTest extends TestCase
{
    use RefreshDatabase;

    // A description with a lead section and two sections that can become the journey section
    private const JOURNEY_DESCRIPTION = '<p>Vooraf.</p><h2>Welkom in Verona</h2><p>De stad.</p>'
        .'<h2>Met de nachttrein naar Verona</h2><p>Een <a href="https://example.com">nachttrein</a>.</p>'
        .'<h2>Aankomst</h2><p>Porta Nuova.</p>';

    private User $admin;

    private Collection $destinations;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->actingAs($this->admin);

        $this->seed(CountrySeeder::class);

        Storage::fake(config('images.disk'));
        Storage::makeDirectory(config('images.directory'));

        $this->destinations = Destination::factory(2)->create();
    }

    public function test_admin_can_view_trip_index(): void
    {
        Trip::factory()->count(3)->create();

        $response = $this->get(route('admin.trips.index'));

        $response->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Admin/Trip/Index')
                ->has('trips.data', 3)
                ->has('trips.links')
        );

        $response->assertStatus(200);
    }

    public function test_admin_can_view_trip_create(): void
    {
        $response = $this->get(route('admin.trips.create'));

        $response->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Admin/Trip/Create')
        );

        $response->assertStatus(200);
    }

    public function test_admin_can_create_a_new_trip(): void
    {
        $tripData = [
            'trip_id' => null,
            'name' => fake()->words(2, true),
            'subtitle' => fake()->text(150),
            'description' => fake()->paragraph(),
            'transport' => [Transport::Train->value],
            'heroImage' => UploadedFile::fake()->image('hero.jpg'),
            'images' => [
                UploadedFile::fake()->image('image1.jpg'),
                UploadedFile::fake()->image('image2.jpg'),
            ],
            'destinations' => $this->destinations->modelKeys(),
            'highlights' => [
                ['title' => 'highlight 1', 'description' => 'description 1'],
                ['title' => 'highlight 2', 'description' => 'description 2'],
                ['title' => 'highlight 3', 'description' => null],
            ],
            'published_at' => now(),
            'meta_title' => fake()->text(60),
            'meta_description' => fake()->text(160),
            'prices' => [
                [
                    'base_price_pp' => fake()->numberBetween(899, 1999),
                    'single_supplement' => fake()->numberBetween(150, 500),
                    'valid_from' => now(),
                    'valid_until' => now()->addMonths(12),
                    'label' => PriceLabel::HighSeason->value,
                ],
            ],
        ];

        $response = $this->post(route('admin.trips.store'), $tripData);

        $trip = Trip::firstOrFail();

        $response->assertRedirect(route('admin.trips.show', $trip));
        $this->assertEquals($tripData['name'], $trip->name);
        $this->assertEquals($tripData['subtitle'], $trip->subtitle);
        $this->assertEquals($tripData['description'], $trip->description);
        $this->assertEquals($tripData['highlights'], $trip->highlights);
        $this->assertTrue($trip->published_at->isSameDay($tripData['published_at']));
        $this->assertCount(2, $trip->destinations);

        $expectedTransport = collect($tripData['transport'])->map(fn ($t) => Transport::from($t)->value)->all();
        $this->assertEqualsCanonicalizing($expectedTransport, $trip->transport);

        // Assert prices
        $this->assertEquals(($tripData['prices'][0]['base_price_pp'] * 100), $trip->prices->first()->base_price_pp);
        $this->assertEquals(($tripData['prices'][0]['single_supplement'] * 100), $trip->prices->first()->single_supplement);
        $this->assertEquals($tripData['prices'][0]['label'], $trip->prices->first()->label->value);
        $this->assertTrue($trip->prices->first()->valid_from->isSameDay($tripData['prices'][0]['valid_from']));
        $this->assertTrue($trip->prices->first()->valid_until->isSameDay($tripData['prices'][0]['valid_until']));

        // Assert hero image with hash-based storage
        $heroImage = $trip->heroImage;
        $this->assertNotNull($heroImage);
        $this->assertEquals($tripData['heroImage']->getClientOriginalName(), $heroImage->original_name);
        $this->assertEquals('image/jpeg', $heroImage->mime_type);
        $this->assertTrue($heroImage->is_primary);
        Storage::disk(config('images.disk'))->assertExists(config('images.directory')."/{$heroImage->path}");

        // Assert gallery images with hash-based storage
        $this->assertCount(2, $trip->images);
        foreach ($trip->images as $index => $image) {
            $originalFile = $tripData['images'][$index];
            $this->assertEquals($originalFile->getClientOriginalName(), $image->original_name);
            $this->assertEquals('image/jpeg', $image->mime_type);
            $this->assertFalse($image->is_primary);
            Storage::disk(config('images.disk'))->assertExists(config('images.directory')."/{$image->path}");
        }
    }

    public function test_admin_can_view_trip_edit(): void
    {
        $trip = Trip::factory()->create();

        $response = $this->get(route('admin.trips.edit', $trip));

        $response->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Admin/Trip/Edit')
                ->has('trip')
                ->where('trip.id', $trip->id)
                ->etc()
        );

        $response->assertStatus(200);
    }

    public function test_admin_can_update_an_existing_trip(): void
    {
        $trip = Trip::factory()->create();
        $updateData = [
            'trip_id' => $trip->id,
            'name' => 'Updated trip name',
            'subtitle' => fake()->text(150),
            'description' => fake()->paragraph(),
            'transport' => array_column([Transport::Bus, Transport::Airplane], 'value'),
            'heroImage' => UploadedFile::fake()->image('updated-featured.jpg'),
            'images' => [
                UploadedFile::fake()->image('new1.jpg'),
                UploadedFile::fake()->image('new2.jpg'),
            ],
            'destinations' => $this->destinations->modelKeys(),
            'highlights' => [
                ['title' => 'updated highlight 1', 'description' => 'updated description 1'],
                ['title' => 'updated highlight 2', 'description' => 'updated description 2'],
                ['title' => 'updated highlight 3', 'description' => null],
            ],
            'published_at' => now()->addDay(fake()->randomDigit())->startOfDay()->toDateTimeString(),
            'meta_title' => fake()->text(60),
            'meta_description' => fake()->text(160),
            'prices' => [
                [
                    'base_price_pp' => fake()->numberBetween(899, 1999),
                    'single_supplement' => fake()->numberBetween(150, 500),
                    'valid_from' => now(),
                    'valid_until' => now()->addMonths(12),
                    'label' => PriceLabel::HighSeason->value,
                ],
            ],
        ];

        $response = $this->post(route('admin.trips.update', $trip), $updateData);
        $trip->refresh();
        $response->assertRedirect(route('admin.trips.show', $trip));

        $this->assertEquals($updateData['name'], $trip->name);
        $this->assertEquals($updateData['subtitle'], $trip->subtitle);
        $this->assertEquals($updateData['description'], $trip->description);
        $this->assertEquals($updateData['published_at'], $trip->published_at);
        $this->assertEquals($updateData['meta_title'], $trip->meta_title);
        $this->assertEquals($updateData['meta_description'], $trip->meta_description);

        $expectedTransport = collect($updateData['transport'])->map(fn ($t) => Transport::from($t)->value)->all();
        $this->assertEqualsCanonicalizing($expectedTransport, $trip->transport);

        // Assert prices
        $this->assertEquals(($updateData['prices'][0]['base_price_pp'] * 100), $trip->prices->first()->base_price_pp);
        $this->assertEquals(($updateData['prices'][0]['single_supplement'] * 100), $trip->prices->first()->single_supplement);
        $this->assertEquals($updateData['prices'][0]['label'], $trip->prices->first()->label->value);
        $this->assertTrue($trip->prices->first()->valid_from->isSameDay($updateData['prices'][0]['valid_from']));
        $this->assertTrue($trip->prices->first()->valid_until->isSameDay($updateData['prices'][0]['valid_until']));

        // Assert featured image with hash-based storage
        $heroImage = $trip->heroImage;
        $this->assertNotNull($heroImage);
        $this->assertEquals($updateData['heroImage']->getClientOriginalName(), $heroImage->original_name);
        $this->assertEquals('image/jpeg', $heroImage->mime_type);
        $this->assertTrue($heroImage->is_primary);
        Storage::disk(config('images.disk'))->assertExists(config('images.directory')."/{$heroImage->path}");

        // Assert gallery images with hash-based storage
        $this->assertCount(2, $trip->images);
        foreach ($trip->images as $index => $image) {
            $originalFile = $updateData['images'][$index];
            $this->assertEquals($originalFile->getClientOriginalName(), $image->original_name);
            $this->assertEquals('image/jpeg', $image->mime_type);
            $this->assertFalse($image->is_primary);
            Storage::disk(config('images.disk'))->assertExists(config('images.directory')."/{$image->path}");
        }
    }

    public function test_admin_can_reorder_trip_images(): void
    {
        $trip = Trip::factory()->create();
        foreach (['a.jpg', 'b.jpg', 'c.jpg'] as $order => $path) {
            $trip->images()->create([
                'path' => $path,
                'original_name' => $path,
                'mime_type' => 'image/jpeg',
                'size' => 1000,
                'order' => $order,
            ]);
        }

        $this->assertEquals(['a.jpg', 'b.jpg', 'c.jpg'], $trip->images()->pluck('path')->all());

        $updateData = [
            'trip_id' => $trip->id,
            'name' => $trip->name,
            'subtitle' => $trip->subtitle,
            'description' => $trip->description,
            'transport' => array_column([Transport::Train], 'value'),
            'destinations' => $this->destinations->modelKeys(),
            'highlights' => [['title' => 'highlight', 'description' => null]],
            'published_at' => now()->addDay()->startOfDay()->toDateTimeString(),
            'meta_title' => fake()->text(60),
            'meta_description' => fake()->text(160),
            'prices' => [
                [
                    'base_price_pp' => 999,
                    'single_supplement' => 150,
                    'valid_from' => now(),
                    'valid_until' => now()->addMonths(12),
                    'label' => PriceLabel::HighSeason->value,
                ],
            ],
            // Reversed order, one image removed (b.jpg) and a new upload in the middle
            'images' => [
                'c.jpg',
                UploadedFile::fake()->image('new.jpg'),
                'a.jpg',
            ],
        ];

        $response = $this->post(route('admin.trips.update', $trip), $updateData);
        $response->assertRedirect(route('admin.trips.show', $trip));

        $images = $trip->refresh()->images;

        $this->assertCount(3, $images);
        $this->assertEquals([0, 1, 2], $images->pluck('order')->all());
        $this->assertEquals('c.jpg', $images[0]->path);
        $this->assertEquals('new.jpg', $images[1]->original_name);
        $this->assertEquals('a.jpg', $images[2]->path);
        $this->assertDatabaseMissing('images', ['path' => 'b.jpg']);
    }

    public function test_practical_info_accessor_returns_all_keys_with_stored_values(): void
    {
        $trip = Trip::factory()->create();

        $trip->update([
            'practical_info' => [
                'travel_period' => 'Juni - September',
                'transport' => 'Trein',
            ],
        ]);

        $trip->refresh();
        $info = $trip->practical_info;

        foreach (PracticalInfo::cases() as $case) {
            $this->assertArrayHasKey($case->value, $info);
        }

        $this->assertEquals('Juni - September', $info['travel_period']);
        $this->assertEquals('Trein', $info['transport']);
        $this->assertSame('', $info['departure_dates']);
        $this->assertSame('', $info['outbound_return']);
        $this->assertSame('', $info['accommodation']);
    }

    public function test_destinations_formatted_accessor(): void
    {
        $trip = Trip::factory()->create();

        // 0 destinations
        $this->assertEquals('', $trip->destinations_formatted);

        // 1 destination
        $d1 = Destination::factory()->withName('Netherlands')->create();
        $trip->destinations()->attach($d1);
        $trip->load('destinations');
        $this->assertEquals('Netherlands', $trip->destinations_formatted);

        // 2 destinations
        $d2 = Destination::factory()->withName('Belgium')->create();
        $trip->destinations()->attach($d2);
        $trip->load('destinations');
        $this->assertEquals('Netherlands & Belgium', $trip->destinations_formatted);

        // 3 destinations
        $d3 = Destination::factory()->withName('Germany')->create();
        $trip->destinations()->attach($d3);
        $trip->load('destinations');
        $this->assertEquals('Netherlands, Belgium & Germany', $trip->destinations_formatted);
    }

    public function test_destinations_formatted_prefers_region_over_name(): void
    {
        $trip = Trip::factory()->create();
        $destination = Destination::factory()->create(['region' => 'Tuscany', 'name' => 'Italy']);
        $trip->destinations()->attach($destination);
        $trip->load('destinations');

        $this->assertEquals('Tuscany, Italy', $trip->destinations_formatted);
    }

    // Highlights validation tests
    public function test_trip_update_drops_blank_highlights(): void
    {
        $trip = Trip::factory()->create();
        $payload = $this->generateTripUpdatePayload($trip, [
            'highlights' => [
                ['title' => 'highlight 1', 'description' => 'description 1'],
                ['title' => 'highlight 2', 'description' => ''],
                ['title' => '', 'description' => ''],
            ],
        ]);

        $response = $this->post(route('admin.trips.update', $trip), $payload);

        $response->assertSessionHasNoErrors();
        $this->assertEquals([
            ['title' => 'highlight 1', 'description' => 'description 1'],
            ['title' => 'highlight 2', 'description' => null],
        ], $trip->fresh()->highlights);
    }

    public function test_trip_update_rejects_a_description_without_title(): void
    {
        $trip = Trip::factory()->create();
        $payload = $this->generateTripUpdatePayload($trip, [
            'highlights' => [
                ['title' => '', 'description' => 'description without a title'],
            ],
        ]);

        $response = $this->post(route('admin.trips.update', $trip), $payload);

        $response->assertSessionHasErrors('highlights.0.title');
    }

    // Subtitle and description section tests
    public function test_trip_show_passes_subtitle_and_description_sections_as_props(): void
    {
        $trip = Trip::factory()->create([
            'subtitle' => 'Met de nachttrein naar Verona',
            'description' => '<p>Vooraf.</p><h2>De reis</h2><p>Een <strong>nachttrein</strong>.</p><h2>De stad</h2><p>Verona.</p>',
        ]);

        $response = $this->get(route('trips.show', $trip));

        $response->assertOk();
        $response->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Trip/Show')
                ->where('trip.subtitle', 'Met de nachttrein naar Verona')
                ->missing('trip.description')
                ->where('descriptionSections', [
                    ['key' => 'de-reis', 'title' => 'De reis', 'html' => '<p>Vooraf.</p><p>Een <strong>nachttrein</strong>.</p>', 'variant' => 'light'],
                    ['key' => 'de-stad', 'title' => 'De stad', 'html' => '<p>Verona.</p>', 'variant' => 'light'],
                ])
        );
    }

    public function test_trip_metadata_ignores_subtitle(): void
    {
        $trip = Trip::factory()->create([
            'subtitle' => 'Unique subtitle marker',
            'description' => '<p>Reis met de trein door Europa.</p>',
            'meta_description' => null,
        ]);

        $response = $this->get(route('trips.show', $trip));

        $response->assertOk();
        $response->assertInertia(
            fn (AssertableInertia $page) => $page
                ->where('seo.description', 'Reis met de trein door Europa.')
        );
        $this->assertStringNotContainsString('Unique subtitle marker', $trip->meta_description);
        $this->assertSame('Reis met de trein door Europa.', $trip->toTouristTripSchema()['description']);
    }

    public function test_trip_metadata_falls_back_to_the_whole_description_across_its_sections(): void
    {
        $trip = Trip::factory()->create([
            'description' => '<h2>De reis</h2><p>Met de trein.</p><h2>De stad</h2><p>Verona.</p>',
            'meta_description' => null,
        ]);

        $response = $this->get(route('trips.show', $trip));

        $response->assertOk();
        $response->assertInertia(
            fn (AssertableInertia $page) => $page
                ->where('seo.description', 'De reis Met de trein. De stad Verona.')
        );
        $this->assertSame('De reis Met de trein. De stad Verona.', $trip->toTouristTripSchema()['description']);
    }

    public function test_trip_update_saves_subtitle(): void
    {
        $trip = Trip::factory()->create();
        $payload = $this->generateTripUpdatePayload($trip, ['subtitle' => 'Updated subtitle']);

        $response = $this->post(route('admin.trips.update', $trip), $payload);

        $response->assertSessionHasNoErrors();
        $this->assertSame('Updated subtitle', $trip->fresh()->subtitle);
    }

    public function test_trip_update_requires_a_subtitle(): void
    {
        $trip = Trip::factory()->create();
        $payload = $this->generateTripUpdatePayload($trip, ['subtitle' => '']);

        $response = $this->post(route('admin.trips.update', $trip), $payload);

        $response->assertSessionHasErrors('subtitle');
    }

    // Key facts tests
    public function test_trip_show_passes_key_facts_with_label_value_and_icon_in_stored_order(): void
    {
        $keyFacts = [
            ['label' => 'Vertrek', 'value' => 'Amsterdam, Utrecht of Arnhem', 'icon' => 'train'],
            ['label' => 'Reistijd', 'value' => 'Ca. 15 uur', 'icon' => 'clock'],
            ['label' => 'Overstap', 'value' => '1x, in Innsbruck', 'icon' => 'transfer'],
        ];
        $trip = Trip::factory()->create(['key_facts' => $keyFacts]);

        $response = $this->get(route('trips.show', $trip));

        $response->assertOk();
        $response->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Trip/Show')
                ->where('trip.key_facts', $keyFacts)
        );
    }

    public function test_trip_show_passes_empty_key_facts_without_any(): void
    {
        $trip = Trip::factory()->create();

        $response = $this->get(route('trips.show', $trip));

        $response->assertOk();
        $response->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Trip/Show')
                ->where('trip.key_facts', [])
        );
    }

    public function test_trip_update_saves_key_facts_in_submitted_order_and_ignores_blank_rows(): void
    {
        $trip = Trip::factory()->create();
        $payload = $this->generateTripUpdatePayload($trip, [
            'key_facts' => [
                ['label' => 'Second', 'value' => 'Second value', 'icon' => 'sun'],
                ['label' => 'First', 'value' => 'First value', 'icon' => 'bed'],
                ['label' => '', 'value' => '', 'icon' => 'info'],
            ],
        ]);

        $response = $this->post(route('admin.trips.update', $trip), $payload);

        $response->assertSessionHasNoErrors();
        $this->assertEquals(
            [
                ['label' => 'Second', 'value' => 'Second value', 'icon' => 'sun'],
                ['label' => 'First', 'value' => 'First value', 'icon' => 'bed'],
            ],
            $trip->fresh()->key_facts
        );
    }

    public function test_trip_update_accepts_a_trip_without_key_facts(): void
    {
        $trip = Trip::factory()->create(['key_facts' => [['label' => 'Vertrek', 'value' => 'Amsterdam', 'icon' => 'train']]]);
        $payload = $this->generateTripUpdatePayload($trip, [
            'key_facts' => [['label' => '', 'value' => '', 'icon' => 'info']],
        ]);

        $response = $this->post(route('admin.trips.update', $trip), $payload);

        $response->assertSessionHasNoErrors();
        $this->assertSame([], $trip->fresh()->key_facts);
    }

    public function test_trip_update_rejects_more_than_the_maximum_number_of_key_facts(): void
    {
        $trip = Trip::factory()->create();
        $payload = $this->generateTripUpdatePayload($trip, [
            'key_facts' => array_map(
                fn (int $i) => ['label' => "Label {$i}", 'value' => "Value {$i}", 'icon' => 'train'],
                range(1, Trip::MAX_KEY_FACTS + 1)
            ),
        ]);

        $response = $this->post(route('admin.trips.update', $trip), $payload);

        $response->assertSessionHasErrors('key_facts');
    }

    public function test_trip_update_rejects_a_key_fact_without_a_label(): void
    {
        $trip = Trip::factory()->create();
        $payload = $this->generateTripUpdatePayload($trip, [
            'key_facts' => [['label' => '', 'value' => 'Amsterdam', 'icon' => 'train']],
        ]);

        $response = $this->post(route('admin.trips.update', $trip), $payload);

        $response->assertSessionHasErrors('key_facts.0.label');
    }

    public function test_trip_update_rejects_a_key_fact_without_a_value(): void
    {
        $trip = Trip::factory()->create();
        $payload = $this->generateTripUpdatePayload($trip, [
            'key_facts' => [['label' => 'Vertrek', 'value' => '', 'icon' => 'train']],
        ]);

        $response = $this->post(route('admin.trips.update', $trip), $payload);

        $response->assertSessionHasErrors('key_facts.0.value');
    }

    public function test_trip_update_rejects_a_key_fact_without_an_icon(): void
    {
        $trip = Trip::factory()->create();
        $payload = $this->generateTripUpdatePayload($trip, [
            'key_facts' => [['label' => 'Vertrek', 'value' => 'Amsterdam']],
        ]);

        $response = $this->post(route('admin.trips.update', $trip), $payload);

        $response->assertSessionHasErrors('key_facts.0.icon');
    }

    public function test_trip_update_rejects_an_unknown_key_fact_icon(): void
    {
        $trip = Trip::factory()->create();
        $payload = $this->generateTripUpdatePayload($trip, [
            'key_facts' => [['label' => 'Vertrek', 'value' => 'Amsterdam', 'icon' => 'rocket']],
        ]);

        $response = $this->post(route('admin.trips.update', $trip), $payload);

        $response->assertSessionHasErrors('key_facts.0.icon');
    }

    public function test_trip_update_rejects_a_key_fact_label_and_value_that_are_too_long(): void
    {
        $trip = Trip::factory()->create();
        $payload = $this->generateTripUpdatePayload($trip, [
            'key_facts' => [
                ['label' => 'Short label', 'value' => 'Short value', 'icon' => 'train'],
                [
                    'label' => str_repeat('a', Trip::MAX_KEY_FACT_LABEL_LENGTH + 1),
                    'value' => str_repeat('a', Trip::MAX_KEY_FACT_VALUE_LENGTH + 1),
                    'icon' => 'train',
                ],
            ],
        ]);

        $response = $this->post(route('admin.trips.update', $trip), $payload);

        $response->assertSessionHasErrors(['key_facts.1.label', 'key_facts.1.value']);
        $response->assertSessionDoesntHaveErrors(['key_facts.0.label', 'key_facts.0.value']);
    }

    public function test_trip_update_accepts_a_key_fact_of_the_maximum_length(): void
    {
        $trip = Trip::factory()->create();
        $keyFact = [
            'label' => str_repeat('a', Trip::MAX_KEY_FACT_LABEL_LENGTH),
            'value' => str_repeat('b', Trip::MAX_KEY_FACT_VALUE_LENGTH),
            'icon' => 'mountain',
        ];
        $payload = $this->generateTripUpdatePayload($trip, ['key_facts' => [$keyFact]]);

        $response = $this->post(route('admin.trips.update', $trip), $payload);

        $response->assertSessionHasNoErrors();
        // MySQL reorders the keys of a JSON column, so compare without regard to key order
        $this->assertEquals([$keyFact], $trip->fresh()->key_facts);
    }

    public function test_trip_edit_passes_key_fact_icon_options(): void
    {
        $trip = Trip::factory()->create();

        $response = $this->get(route('admin.trips.edit', $trip));

        $response->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Admin/Trip/Edit')
                ->has('keyFactIconOptions', count(KeyFactIcon::cases()))
        );
    }

    public function test_key_facts_migration_moves_existing_text_to_value(): void
    {
        $trip = Trip::factory()->create();
        DB::table('trips')->where('id', $trip->id)->update([
            'key_facts' => json_encode(['Vertrek vanaf Amsterdam', 'Ca. 15 uur']),
        ]);
        $withoutKeyFacts = Trip::factory()->create();

        $migration = require database_path('migrations/2026_10_06_140000_convert_key_facts_to_label_value_icon.php');
        $migration->up();

        $this->assertEquals([
            ['label' => '', 'value' => 'Vertrek vanaf Amsterdam', 'icon' => KeyFactIcon::default()->value],
            ['label' => '', 'value' => 'Ca. 15 uur', 'icon' => KeyFactIcon::default()->value],
        ], $trip->fresh()->key_facts);
        $this->assertSame([], $withoutKeyFacts->fresh()->key_facts);

        $migration->down();

        $this->assertSame(
            ['Vertrek vanaf Amsterdam', 'Ca. 15 uur'],
            json_decode(DB::table('trips')->where('id', $trip->id)->value('key_facts'), true)
        );
    }

    // Blocked dates validation tests
    public function test_trip_update_accepts_a_single_blocked_date(): void
    {
        $trip = Trip::factory()->create();
        $payload = $this->generateTripUpdatePayload($trip, [
            'blocked_dates' => [
                'dates' => [now()->addMonth()->format('Y-m-d')],
                'weekdays' => [],
            ],
        ]);

        $response = $this->post(route('admin.trips.update', $trip), $payload);

        $response->assertSessionHasNoErrors();
    }

    public function test_trip_update_accepts_a_blocked_date_range(): void
    {
        $trip = Trip::factory()->create();
        $payload = $this->generateTripUpdatePayload($trip, [
            'blocked_dates' => [
                'dates' => [
                    ['start' => now()->addMonth()->format('Y-m-d'), 'end' => now()->addMonths(2)->format('Y-m-d')],
                ],
                'weekdays' => [],
            ],
        ]);

        $response = $this->post(route('admin.trips.update', $trip), $payload);

        $response->assertSessionHasNoErrors();
    }

    public function test_trip_update_accepts_blocked_weekdays(): void
    {
        $trip = Trip::factory()->create();
        $payload = $this->generateTripUpdatePayload($trip, [
            'blocked_dates' => [
                'dates' => [],
                'weekdays' => [0, 6],
            ],
        ]);

        $response = $this->post(route('admin.trips.update', $trip), $payload);

        $response->assertSessionHasNoErrors();
    }

    public function test_trip_update_accepts_null_blocked_dates(): void
    {
        $trip = Trip::factory()->create();
        $payload = $this->generateTripUpdatePayload($trip, ['blocked_dates' => null]);

        $response = $this->post(route('admin.trips.update', $trip), $payload);

        $response->assertSessionHasNoErrors();
    }

    public function test_trip_update_rejects_a_blocked_date_in_the_past(): void
    {
        $trip = Trip::factory()->create();
        $payload = $this->generateTripUpdatePayload($trip, [
            'blocked_dates' => [
                'dates' => [now()->subDay()->format('Y-m-d')],
                'weekdays' => [],
            ],
        ]);

        $response = $this->post(route('admin.trips.update', $trip), $payload);

        $response->assertSessionHasErrors('blocked_dates.dates.0');
    }

    public function test_trip_update_rejects_invalid_weekday(): void
    {
        $trip = Trip::factory()->create();
        $payload = $this->generateTripUpdatePayload($trip, [
            'blocked_dates' => [
                'dates' => [],
                'weekdays' => [7],
            ],
        ]);

        $response = $this->post(route('admin.trips.update', $trip), $payload);

        $response->assertSessionHasErrors('blocked_dates.weekdays.0');
    }

    public function test_trip_update_rejects_range_with_end_before_start(): void
    {
        $trip = Trip::factory()->create();
        $payload = $this->generateTripUpdatePayload($trip, [
            'blocked_dates' => [
                'dates' => [
                    ['start' => now()->addMonths(2)->format('Y-m-d'), 'end' => now()->addMonth()->format('Y-m-d')],
                ],
                'weekdays' => [],
            ],
        ]);

        $response = $this->post(route('admin.trips.update', $trip), $payload);

        $response->assertSessionHasErrors('blocked_dates.dates.0.end');
    }

    public function test_trip_update_rejects_range_with_start_in_the_past(): void
    {
        $trip = Trip::factory()->create();
        $payload = $this->generateTripUpdatePayload($trip, [
            'blocked_dates' => [
                'dates' => [
                    ['start' => now()->subDay()->format('Y-m-d'), 'end' => now()->addMonth()->format('Y-m-d')],
                ],
                'weekdays' => [],
            ],
        ]);

        $response = $this->post(route('admin.trips.update', $trip), $payload);

        $response->assertSessionHasErrors('blocked_dates.dates.0.start');
    }

    public function test_trip_update_normalizes_missing_dates_to_empty_array(): void
    {
        $trip = Trip::factory()->create();
        $payload = $this->generateTripUpdatePayload($trip, [
            'blocked_dates' => ['weekdays' => [1]],
        ]);

        $response = $this->post(route('admin.trips.update', $trip), $payload);

        $response->assertSessionHasNoErrors();
        $this->assertSame([], $trip->refresh()->blocked_dates['dates']);
    }

    public function test_trip_update_normalizes_missing_weekdays_to_empty_array(): void
    {
        $trip = Trip::factory()->create();
        $payload = $this->generateTripUpdatePayload($trip, [
            'blocked_dates' => ['dates' => [now()->addMonth()->format('Y-m-d')]],
        ]);

        $response = $this->post(route('admin.trips.update', $trip), $payload);

        $response->assertSessionHasNoErrors();
        $this->assertSame([], $trip->refresh()->blocked_dates['weekdays']);
    }

    public function test_meta_description_falls_back_to_plain_text_description(): void
    {
        $trip = Trip::factory()->create([
            'description' => '<p>'.str_repeat('Reis met de trein door Europa. ', 20).'</p>',
            'meta_description' => null,
        ]);

        $metaDescription = $trip->meta_description;

        $this->assertStringNotContainsString('<', $metaDescription);
        $this->assertStringStartsWith('Reis met de trein door Europa.', $metaDescription);
        $this->assertLessThanOrEqual(config('seo.meta_description_max_length'), strlen($metaDescription));
    }

    // Journey section tests

    public function test_trip_update_saves_a_journey_section_from_the_submitted_description(): void
    {
        $trip = Trip::factory()->create(['description' => '<h2>Welkom in Verona</h2><p>De stad.</p>']);
        $payload = $this->generateTripUpdatePayload($trip, [
            'description' => self::JOURNEY_DESCRIPTION,
            'journey_section' => 'met-de-nachttrein-naar-verona',
        ]);

        $response = $this->post(route('admin.trips.update', $trip), $payload);

        $response->assertSessionHasNoErrors();
        $this->assertSame('met-de-nachttrein-naar-verona', $trip->fresh()->journey_section);
    }

    public function test_trip_update_saves_an_empty_journey_section_as_null(): void
    {
        $trip = Trip::factory()->create([
            'description' => self::JOURNEY_DESCRIPTION,
            'journey_section' => 'aankomst',
        ]);
        $payload = $this->generateTripUpdatePayload($trip, ['journey_section' => '']);

        $response = $this->post(route('admin.trips.update', $trip), $payload);

        $response->assertSessionHasNoErrors();
        $this->assertNull($trip->fresh()->journey_section);
    }

    public function test_trip_update_rejects_a_journey_section_that_is_not_in_the_submitted_description(): void
    {
        // The key exists in the saved description, but no longer in the submitted one
        $trip = Trip::factory()->create(['description' => self::JOURNEY_DESCRIPTION]);
        $payload = $this->generateTripUpdatePayload($trip, [
            'description' => '<h2>Welkom in Verona</h2><p>De stad.</p><h2>Met de dagtrein naar Verona</h2><p>Overdag.</p>',
            'journey_section' => 'met-de-nachttrein-naar-verona',
        ]);

        $response = $this->post(route('admin.trips.update', $trip), $payload);

        $response->assertSessionHasErrors('journey_section');
        $this->assertNull($trip->fresh()->journey_section);
    }

    public function test_trip_update_rejects_the_first_section_as_journey_section(): void
    {
        $trip = Trip::factory()->create(['description' => self::JOURNEY_DESCRIPTION]);
        $payload = $this->generateTripUpdatePayload($trip, ['journey_section' => 'welkom-in-verona']);

        $response = $this->post(route('admin.trips.update', $trip), $payload);

        $response->assertSessionHasErrors('journey_section');
    }

    public function test_trip_store_rejects_a_journey_section_that_is_not_in_the_description(): void
    {
        $response = $this->post(route('admin.trips.store'), [
            'description' => self::JOURNEY_DESCRIPTION,
            'journey_section' => 'onbekende-sectie',
        ]);

        $response->assertSessionHasErrors('journey_section');
    }

    public function test_trip_show_gives_the_journey_section_the_dark_variant(): void
    {
        $trip = Trip::factory()->create([
            'description' => self::JOURNEY_DESCRIPTION,
            'journey_section' => 'met-de-nachttrein-naar-verona',
        ]);

        $response = $this->get(route('trips.show', $trip));

        $response->assertOk();
        $response->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Trip/Show')
                ->where('descriptionSections.0.key', 'welkom-in-verona')
                ->where('descriptionSections.0.variant', 'light')
                ->where('descriptionSections.1.key', 'met-de-nachttrein-naar-verona')
                ->where('descriptionSections.1.variant', 'dark')
                ->where('descriptionSections.2.key', 'aankomst')
                ->where('descriptionSections.2.variant', 'light')
        );
    }

    public function test_trip_show_keeps_all_sections_light_for_an_outdated_journey_section(): void
    {
        // The title of the journey section was changed after it was chosen
        $trip = Trip::factory()->create([
            'description' => self::JOURNEY_DESCRIPTION,
            'journey_section' => 'met-de-dagtrein-naar-verona',
        ]);

        $response = $this->get(route('trips.show', $trip));

        $response->assertOk();
        $response->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Trip/Show')
                ->has('descriptionSections', 3)
                ->where('descriptionSections.0.variant', 'light')
                ->where('descriptionSections.1.variant', 'light')
                ->where('descriptionSections.2.variant', 'light')
        );
    }

    public function test_trip_show_keeps_the_first_section_light_as_journey_section(): void
    {
        // The chosen section became the first one after the sections were reordered
        $trip = Trip::factory()->create([
            'description' => self::JOURNEY_DESCRIPTION,
            'journey_section' => 'welkom-in-verona',
        ]);

        $response = $this->get(route('trips.show', $trip));

        $response->assertOk();
        $response->assertInertia(
            fn (AssertableInertia $page) => $page
                ->where('descriptionSections.0.key', 'welkom-in-verona')
                ->where('descriptionSections.0.variant', 'light')
        );
    }

    public function test_trip_edit_passes_the_sections_after_the_first_as_journey_section_options(): void
    {
        $trip = Trip::factory()->create(['description' => self::JOURNEY_DESCRIPTION]);

        $response = $this->get(route('admin.trips.edit', $trip));

        $response->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Admin/Trip/Edit')
                ->where('journeySectionOptions', [
                    ['id' => 'met-de-nachttrein-naar-verona', 'name' => 'Met de nachttrein naar Verona'],
                    ['id' => 'aankomst', 'name' => 'Aankomst'],
                ])
        );
    }

    public function test_description_sections_returns_the_titled_sections_after_the_first_for_the_edited_description(): void
    {
        $response = $this->postJson(route('admin.trips.description-sections'), [
            'description' => self::JOURNEY_DESCRIPTION.'<h2></h2><p>Zonder titel.</p>',
        ]);

        $response->assertOk();
        $response->assertExactJson([
            ['id' => 'met-de-nachttrein-naar-verona', 'name' => 'Met de nachttrein naar Verona'],
            ['id' => 'aankomst', 'name' => 'Aankomst'],
        ]);
    }

    public function test_description_sections_returns_no_options_for_an_empty_description(): void
    {
        $response = $this->postJson(route('admin.trips.description-sections'), ['description' => null]);

        $response->assertOk();
        $response->assertExactJson([]);
    }

    public function test_description_sections_is_forbidden_for_a_non_admin_user(): void
    {
        $this->actingAs(User::factory()->create(['role' => UserRole::Guest]));

        $response = $this->postJson(route('admin.trips.description-sections'), [
            'description' => self::JOURNEY_DESCRIPTION,
        ]);

        $response->assertForbidden();
    }

    // Helper Methods

    private function generateTripUpdatePayload(Trip $trip, array $overrides = []): array
    {
        return array_merge([
            'name' => $trip->name,
            'subtitle' => $trip->subtitle,
            'description' => $trip->description,
            'published_at' => $trip->published_at->toDateTimeString(),
            'destinations' => $this->destinations->modelKeys(),
            'highlights' => [['title' => 'highlight 1', 'description' => null]],
            'meta_title' => $trip->meta_title,
            'meta_description' => $trip->meta_description,
            'prices' => [
                [
                    'base_price_pp' => fake()->numberBetween(899, 1999),
                    'single_supplement' => fake()->numberBetween(150, 500),
                    'valid_from' => now(),
                    'valid_until' => now()->addMonths(12),
                    'label' => PriceLabel::HighSeason->value,
                ],
            ],
        ], $overrides);
    }

    // Duration auto-calculation tests

    public function test_new_trip_has_zero_duration_without_itineraries(): void
    {
        $trip = Trip::factory()->create();

        $this->assertEquals(0, $trip->duration);
    }

    public function test_duration_is_set_from_day_from_when_itinerary_is_created(): void
    {
        $trip = Trip::factory()->create();

        Itinerary::factory()->create(['trip_id' => $trip->id, 'day_from' => 3, 'day_to' => null]);

        $this->assertEquals(3, $trip->fresh()->duration);
    }

    public function test_duration_uses_day_to_when_set(): void
    {
        $trip = Trip::factory()->create();

        Itinerary::factory()->create(['trip_id' => $trip->id, 'day_from' => 2, 'day_to' => 5]);

        $this->assertEquals(5, $trip->fresh()->duration);
    }

    public function test_duration_reflects_max_across_all_itineraries(): void
    {
        $trip = Trip::factory()->create();

        Itinerary::factory()->create(['trip_id' => $trip->id, 'day_from' => 1, 'day_to' => null]);
        Itinerary::factory()->create(['trip_id' => $trip->id, 'day_from' => 2, 'day_to' => 4]);

        $this->assertEquals(4, $trip->fresh()->duration);
    }

    public function test_duration_recalculates_when_highest_itinerary_is_deleted(): void
    {
        $trip = Trip::factory()->create();

        Itinerary::factory()->create(['trip_id' => $trip->id, 'day_from' => 1, 'day_to' => null, 'order' => 1]);
        $highest = Itinerary::factory()->create(['trip_id' => $trip->id, 'day_from' => 2, 'day_to' => 4, 'order' => 2]);

        $this->assertEquals(4, $trip->fresh()->duration);

        $highest->delete();

        $this->assertEquals(1, $trip->fresh()->duration);
    }

    public function test_duration_recalculates_when_itinerary_is_updated(): void
    {
        $trip = Trip::factory()->create();

        $itinerary = Itinerary::factory()->create(['trip_id' => $trip->id, 'day_from' => 1, 'day_to' => 3]);
        $this->assertEquals(3, $trip->fresh()->duration);

        $itinerary->update(['day_to' => 7]);

        $this->assertEquals(7, $trip->fresh()->duration);
    }

    public function test_admin_can_softdelete_a_trip(): void
    {
        $trip = Trip::factory()->create();
        $image = Image::factory()->create([
            'imageable_id' => $trip->id,
            'imageable_type' => Trip::class,
        ]);

        foreach ([ItemType::Inclusion, ItemType::Exclusion] as $type) {
            TripItem::create([
                'trip_id' => $trip->id,
                'type' => $type,
                'item' => fake()->sentence(),
            ]);
        }

        $response = $this->delete(route('admin.trips.destroy', $trip));

        $response->assertRedirect(route('admin.trips.index'));

        $this->assertSoftDeleted($trip);
        $this->assertSoftDeleted($image);

        $this->assertDatabaseMissing('trips', [
            'id' => $trip->id,
            'deleted_at' => null,
        ]);
        $this->assertDatabaseMissing('images', [
            'imageable_id' => $trip->id,
            'deleted_at' => null,
        ]);
        $this->assertDatabaseMissing('trip_items', [
            'trip_id' => $trip->id,
        ]);
    }
}
