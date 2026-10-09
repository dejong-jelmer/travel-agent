<?php

namespace Tests\Feature;

use App\Enums\Transport;
use App\Enums\Trip\HighlightCategory;
use App\Enums\Trip\ItemType;
use App\Enums\Trip\ItineraryType;
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
        $this->assertEquals(
            array_map(fn (array $highlight) => [...$highlight, 'category' => null, 'label' => null], $tripData['highlights']),
            $trip->highlights
        );
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
            ['title' => 'highlight 1', 'description' => 'description 1', 'category' => null, 'label' => null],
            ['title' => 'highlight 2', 'description' => null, 'category' => null, 'label' => null],
        ], $trip->fresh()->highlights);
    }

    public function test_trip_update_saves_highlight_category_and_own_label(): void
    {
        $trip = Trip::factory()->create();
        $payload = $this->generateTripUpdatePayload($trip, [
            'highlights' => [
                ['title' => 'Arena di Verona', 'description' => null, 'category' => 'roman', 'label' => 'Romeins theater'],
                ['title' => 'Lago di Garda', 'description' => null, 'category' => 'water', 'label' => ''],
                ['title' => 'Piazza delle Erbe', 'description' => null, 'category' => '', 'label' => ''],
            ],
        ]);

        $response = $this->post(route('admin.trips.update', $trip), $payload);

        $response->assertSessionHasNoErrors();
        $this->assertEquals([
            ['title' => 'Arena di Verona', 'description' => null, 'category' => HighlightCategory::Roman, 'label' => 'Romeins theater'],
            ['title' => 'Lago di Garda', 'description' => null, 'category' => HighlightCategory::Water, 'label' => null],
            ['title' => 'Piazza delle Erbe', 'description' => null, 'category' => null, 'label' => null],
        ], $trip->fresh()->highlights);
    }

    public function test_trip_update_rejects_an_unknown_highlight_category(): void
    {
        $trip = Trip::factory()->create();
        $payload = $this->generateTripUpdatePayload($trip, [
            'highlights' => [
                ['title' => 'Arena di Verona', 'description' => null, 'category' => 'colosseum', 'label' => null],
            ],
        ]);

        $response = $this->post(route('admin.trips.update', $trip), $payload);

        $response->assertSessionHasErrors('highlights.0.category');
    }

    public function test_trip_update_rejects_a_highlight_label_that_is_too_long(): void
    {
        $trip = Trip::factory()->create();
        $payload = $this->generateTripUpdatePayload($trip, [
            'highlights' => [
                ['title' => 'Arena di Verona', 'description' => null, 'category' => 'roman', 'label' => str_repeat('a', Trip::MAX_HIGHLIGHT_LABEL_LENGTH)],
                ['title' => 'Teatro Romano', 'description' => null, 'category' => 'roman', 'label' => str_repeat('a', Trip::MAX_HIGHLIGHT_LABEL_LENGTH + 1)],
            ],
        ]);

        $response = $this->post(route('admin.trips.update', $trip), $payload);

        $response->assertSessionDoesntHaveErrors('highlights.0.label');
        $response->assertSessionHasErrors('highlights.1.label');
    }

    public function test_trip_update_rejects_an_own_label_without_category(): void
    {
        $trip = Trip::factory()->create();
        $payload = $this->generateTripUpdatePayload($trip, [
            'highlights' => [
                ['title' => 'Arena di Verona', 'description' => null, 'category' => '', 'label' => 'Romeins theater'],
            ],
        ]);

        $response = $this->post(route('admin.trips.update', $trip), $payload);

        $response->assertSessionHasErrors('highlights.0.category');
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
                    ['key' => 'de-reis', 'title' => 'De reis', 'html' => '<p>Vooraf.</p><p>Een <strong>nachttrein</strong>.</p>', 'variant' => 'light', 'image_id' => null],
                    ['key' => 'de-stad', 'title' => 'De stad', 'html' => '<p>Verona.</p>', 'variant' => 'light', 'image_id' => null],
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

    // Highlights display tests
    public function test_trip_show_passes_highlights_with_the_icon_and_default_label_of_their_category(): void
    {
        $trip = Trip::factory()->create(['highlights' => [
            ['title' => 'Arena di Verona', 'description' => 'Een Romeins amfitheater midden in de stad.', 'category' => 'roman'],
            ['title' => 'Lascaux IV', 'description' => null, 'category' => 'prehistory'],
            ['title' => 'Grotte de Font-de-Gaume', 'description' => null, 'category' => 'cave'],
        ]]);

        $response = $this->get(route('trips.show', $trip));

        $response->assertOk();
        $response->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Trip/Show')
                ->missing('trip.highlights')
                ->where('highlights', [
                    [
                        'title' => 'Arena di Verona',
                        'description' => 'Een Romeins amfitheater midden in de stad.',
                        'category' => 'roman',
                        'icon' => 'Landmark',
                        'label' => HighlightCategory::Roman->label(),
                    ],
                    [
                        'title' => 'Lascaux IV',
                        'description' => null,
                        'category' => 'prehistory',
                        'icon' => 'LascauxHorse',
                        'label' => HighlightCategory::Prehistory->label(),
                    ],
                    [
                        'title' => 'Grotte de Font-de-Gaume',
                        'description' => null,
                        'category' => 'cave',
                        'icon' => 'Cave',
                        'label' => HighlightCategory::Cave->label(),
                    ],
                ])
        );
    }

    public function test_trip_show_passes_the_own_label_of_a_highlight_instead_of_the_default_label(): void
    {
        $trip = Trip::factory()->create(['highlights' => [
            ['title' => 'Arena di Verona', 'description' => null, 'category' => 'roman', 'label' => 'Romeins theater'],
        ]]);

        $response = $this->get(route('trips.show', $trip));

        $response->assertOk();
        $response->assertInertia(
            fn (AssertableInertia $page) => $page
                ->where('highlights.0.icon', 'Landmark')
                ->where('highlights.0.label', 'Romeins theater')
        );
    }

    public function test_trip_show_passes_no_icon_and_no_label_for_a_highlight_without_category(): void
    {
        // A highlight stored before categories existed has no category or label keys at all
        $trip = Trip::factory()->create();
        DB::table('trips')->where('id', $trip->id)->update([
            'highlights' => json_encode([['title' => 'Lago di Garda', 'description' => 'Het grootste meer van Italië.']]),
        ]);

        $response = $this->get(route('trips.show', $trip));

        $response->assertOk();
        $response->assertInertia(
            fn (AssertableInertia $page) => $page
                ->where('highlights', [
                    [
                        'title' => 'Lago di Garda',
                        'description' => 'Het grootste meer van Italië.',
                        'category' => null,
                        'icon' => null,
                        'label' => null,
                    ],
                ])
        );
    }

    public function test_trip_show_passes_empty_highlights_without_any(): void
    {
        $trip = Trip::factory()->create(['highlights' => []]);

        $response = $this->get(route('trips.show', $trip));

        $response->assertOk();
        $response->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Trip/Show')
                ->where('highlights', [])
        );
    }

    // Itinerary display tests
    public function test_trip_show_passes_the_itinerary_in_the_order_set_in_the_admin(): void
    {
        $trip = Trip::factory()->create();
        $stay = Itinerary::factory()->create([
            'trip_id' => $trip->id,
            'type' => ItineraryType::Stay,
            'day_from' => 2,
            'day_to' => 4,
            'title' => 'Drie dagen Verona',
            'remark' => 'Lunch niet inbegrepen',
            'order' => 2,
        ]);
        $train = Itinerary::factory()->create(['trip_id' => $trip->id, 'type' => ItineraryType::Train, 'day_from' => 5, 'order' => 3]);
        $nightTrain = Itinerary::factory()->create(['trip_id' => $trip->id, 'type' => ItineraryType::NightTrain, 'day_from' => 1, 'order' => 1]);
        $image = Image::factory()->create(['imageable_id' => $stay->id, 'imageable_type' => Itinerary::class]);

        $response = $this->get(route('trips.show', $trip));

        $response->assertOk();
        $response->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Trip/Show')
                ->missing('trip.itineraries')
                ->has('itinerary', 3)
                ->where('itinerary.0.id', $nightTrain->id)
                ->where('itinerary.0.type', 'night_train')
                ->where('itinerary.0.day_from', 1)
                ->where('itinerary.0.day_to', null)
                ->where('itinerary.0.image', null)
                ->where('itinerary.1.id', $stay->id)
                ->where('itinerary.1.type', 'stay')
                ->where('itinerary.1.day_from', 2)
                ->where('itinerary.1.day_to', 4)
                ->where('itinerary.1.title', 'Drie dagen Verona')
                ->where('itinerary.1.remark', 'Lunch niet inbegrepen')
                ->where('itinerary.1.image.id', $image->id)
                ->where('itinerary.1.image.public_url', $image->public_url)
                ->has('itinerary.1.image.sources')
                ->where('itinerary.2.id', $train->id)
                ->where('itinerary.2.type', 'train')
                ->where('itinerary.2.day_from', 5)
        );
    }

    public function test_trip_show_passes_an_empty_itinerary_without_any_items(): void
    {
        $trip = Trip::factory()->create();

        $response = $this->get(route('trips.show', $trip));

        $response->assertOk();
        $response->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Trip/Show')
                ->missing('trip.itineraries')
                ->where('itinerary', [])
        );
    }

    public function test_trip_show_gives_the_accommodation_label_only_to_the_first_item_of_each_run_with_the_same_accommodation(): void
    {
        $trip = Trip::factory()->create();
        $items = [
            ['day_from' => 1, 'day_to' => null, 'accommodation' => 'Hotel Aurora'],
            ['day_from' => 2, 'day_to' => 4, 'accommodation' => 'Hotel Aurora'],
            ['day_from' => 5, 'day_to' => null, 'accommodation' => 'Hotel Gabbia'],
            ['day_from' => 6, 'day_to' => null, 'accommodation' => null],
            ['day_from' => 7, 'day_to' => null, 'accommodation' => 'Hotel Gabbia'],
            ['day_from' => 8, 'day_to' => null, 'accommodation' => 'Hotel Aurora'],
        ];
        foreach ($items as $index => $item) {
            Itinerary::factory()->create([...$item, 'trip_id' => $trip->id, 'order' => $index + 1]);
        }

        $response = $this->get(route('trips.show', $trip));

        $response->assertOk();
        $response->assertInertia(
            fn (AssertableInertia $page) => $page
                // A run counts every day of a range: day 1 plus days 2-4
                ->where('itinerary.0.accommodation_label', 'Je verblijft 4 nachten in Hotel Aurora')
                ->where('itinerary.1.accommodation_label', null)
                ->where('itinerary.2.accommodation_label', 'Je verblijft in Hotel Gabbia')
                ->where('itinerary.3.accommodation_label', null)
                // A day without accommodation ends the run, so the same accommodation after it starts a new one
                ->where('itinerary.4.accommodation_label', 'Je verblijft in Hotel Gabbia')
                ->where('itinerary.5.accommodation_label', 'Je verblijft in Hotel Aurora')
        );
    }

    public function test_trip_show_counts_every_day_of_a_single_range_in_the_accommodation_label(): void
    {
        $trip = Trip::factory()->create();
        Itinerary::factory()->create(['trip_id' => $trip->id, 'day_from' => 3, 'day_to' => 6, 'accommodation' => 'Hotel Aurora']);

        $response = $this->get(route('trips.show', $trip));

        $response->assertOk();
        $response->assertInertia(
            fn (AssertableInertia $page) => $page
                ->where('itinerary.0.accommodation_label', 'Je verblijft 4 nachten in Hotel Aurora')
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

    // Section image tests

    public function test_trip_update_saves_a_photo_for_the_sections_after_the_first(): void
    {
        $trip = Trip::factory()->create(['description' => self::JOURNEY_DESCRIPTION]);
        [$first, $second] = $this->createGalleryImages($trip, 2);
        $payload = $this->generateTripUpdatePayload($trip, [
            'section_images' => [
                'met-de-nachttrein-naar-verona' => (string) $second->id,
                'aankomst' => (string) $first->id,
            ],
        ]);

        $response = $this->post(route('admin.trips.update', $trip), $payload);

        $response->assertSessionHasNoErrors();
        // The database may store the keys of a JSON object in another order
        $sectionImages = $trip->fresh()->section_images;
        $this->assertCount(2, $sectionImages);
        $this->assertSame($second->id, $sectionImages['met-de-nachttrein-naar-verona']);
        $this->assertSame($first->id, $sectionImages['aankomst']);
    }

    public function test_trip_update_removes_the_photo_of_a_section_set_to_no_photo(): void
    {
        $trip = Trip::factory()->create(['description' => self::JOURNEY_DESCRIPTION]);
        [$image] = $this->createGalleryImages($trip, 1);
        $trip->update(['section_images' => ['aankomst' => $image->id]]);
        $payload = $this->generateTripUpdatePayload($trip, ['section_images' => ['aankomst' => '']]);

        $response = $this->post(route('admin.trips.update', $trip), $payload);

        $response->assertSessionHasNoErrors();
        $this->assertSame([], $trip->fresh()->section_images);
    }

    public function test_trip_update_rejects_a_section_photo_of_another_trip(): void
    {
        $trip = Trip::factory()->create(['description' => self::JOURNEY_DESCRIPTION]);
        $this->createGalleryImages($trip, 1);
        [$otherImage] = $this->createGalleryImages(Trip::factory()->create(), 1);
        $payload = $this->generateTripUpdatePayload($trip, [
            'section_images' => ['aankomst' => (string) $otherImage->id],
        ]);

        $response = $this->post(route('admin.trips.update', $trip), $payload);

        $response->assertSessionHasErrors('section_images.aankomst');
        $this->assertSame([], $trip->fresh()->section_images);
    }

    public function test_trip_update_rejects_the_hero_image_as_section_photo(): void
    {
        $trip = Trip::factory()->create(['description' => self::JOURNEY_DESCRIPTION]);
        $heroImage = $trip->heroImage()->create([
            'path' => 'hero.jpg',
            'original_name' => 'hero.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 1000,
            'is_primary' => true,
        ]);
        $payload = $this->generateTripUpdatePayload($trip, [
            'section_images' => ['aankomst' => (string) $heroImage->id],
        ]);

        $response = $this->post(route('admin.trips.update', $trip), $payload);

        $response->assertSessionHasErrors('section_images.aankomst');
    }

    public function test_trip_update_ignores_and_cleans_up_photos_of_sections_no_longer_in_the_description(): void
    {
        $trip = Trip::factory()->create(['description' => self::JOURNEY_DESCRIPTION]);
        [$first, $second] = $this->createGalleryImages($trip, 2);
        [$otherImage] = $this->createGalleryImages(Trip::factory()->create(), 1);
        $trip->update(['section_images' => ['met-de-nachttrein-naar-verona' => $first->id]]);

        // The journey heading was renamed, and the form still holds the photos of the old key and an unknown one
        $payload = $this->generateTripUpdatePayload($trip, [
            'description' => '<h2>Welkom in Verona</h2><p>De stad.</p><h2>Met de dagtrein naar Verona</h2><p>Overdag.</p>',
            'section_images' => [
                'met-de-nachttrein-naar-verona' => (string) $first->id,
                'met-de-dagtrein-naar-verona' => (string) $second->id,
                'onbekende-sectie' => (string) $otherImage->id,
            ],
        ]);

        $response = $this->post(route('admin.trips.update', $trip), $payload);

        $response->assertSessionHasNoErrors();
        $this->assertSame(['met-de-dagtrein-naar-verona' => $second->id], $trip->fresh()->section_images);
    }

    public function test_trip_update_ignores_a_photo_for_the_first_section(): void
    {
        $trip = Trip::factory()->create(['description' => self::JOURNEY_DESCRIPTION]);
        [$image] = $this->createGalleryImages($trip, 1);
        $payload = $this->generateTripUpdatePayload($trip, [
            'section_images' => ['welkom-in-verona' => (string) $image->id],
        ]);

        $response = $this->post(route('admin.trips.update', $trip), $payload);

        $response->assertSessionHasNoErrors();
        $this->assertSame([], $trip->fresh()->section_images);
    }

    public function test_trip_update_drops_the_photo_of_a_section_when_it_is_removed_from_the_gallery(): void
    {
        $trip = Trip::factory()->create(['description' => self::JOURNEY_DESCRIPTION]);
        [$removed, $kept] = $this->createGalleryImages($trip, 2);
        $payload = $this->generateTripUpdatePayload($trip, [
            'images' => [$kept->path],
            'section_images' => [
                'met-de-nachttrein-naar-verona' => (string) $kept->id,
                'aankomst' => (string) $removed->id,
            ],
        ]);

        $response = $this->post(route('admin.trips.update', $trip), $payload);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('images', ['id' => $removed->id]);
        $this->assertSame(['met-de-nachttrein-naar-verona' => $kept->id], $trip->fresh()->section_images);
    }

    public function test_trip_show_passes_the_photo_of_each_section(): void
    {
        $trip = Trip::factory()->create(['description' => self::JOURNEY_DESCRIPTION]);
        [, $image] = $this->createGalleryImages($trip, 2);
        $trip->update(['section_images' => ['aankomst' => $image->id]]);

        $response = $this->get(route('trips.show', $trip));

        $response->assertOk();
        $response->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Trip/Show')
                ->where('descriptionSections.0.image_id', null)
                ->where('descriptionSections.1.image_id', null)
                ->where('descriptionSections.2.key', 'aankomst')
                ->where('descriptionSections.2.image_id', $image->id)
        );
    }

    public function test_trip_show_passes_no_photo_for_the_first_section_or_an_image_outside_the_gallery(): void
    {
        $trip = Trip::factory()->create(['description' => self::JOURNEY_DESCRIPTION]);
        [$image] = $this->createGalleryImages($trip, 1);
        [$otherImage] = $this->createGalleryImages(Trip::factory()->create(), 1);
        // Stored before the sections were reordered, or before the image was removed from the gallery
        $trip->update(['section_images' => [
            'welkom-in-verona' => $image->id,
            'aankomst' => $otherImage->id,
        ]]);

        $response = $this->get(route('trips.show', $trip));

        $response->assertOk();
        $response->assertInertia(
            fn (AssertableInertia $page) => $page
                ->where('descriptionSections.0.image_id', null)
                ->where('descriptionSections.2.image_id', null)
        );
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

    /**
     * @return \Illuminate\Support\Collection<int, Image>
     */
    private function createGalleryImages(Trip $trip, int $count): \Illuminate\Support\Collection
    {
        return collect(range(1, $count))->map(fn (int $number) => $trip->images()->create([
            'path' => "gallery-{$trip->id}-{$number}.jpg",
            'original_name' => "gallery-{$number}.jpg",
            'mime_type' => 'image/jpeg',
            'size' => 1000,
            'order' => $number - 1,
        ]));
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
