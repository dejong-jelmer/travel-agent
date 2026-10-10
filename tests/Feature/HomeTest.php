<?php

namespace Tests\Feature;

use App\Enums\Trip\ItineraryType;
use App\Mail\AdminContactFormNotificationMail;
use App\Models\Destination;
use App\Models\Itinerary;
use App\Models\Trip;
use App\Models\TripPrice;
use App\Services\TermsPdfService;
use Database\Seeders\CountrySeeder;
use Faker\Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CountrySeeder::class);
    }

    public function test_home_page_shows_trips()
    {
        $destination = Destination::factory()->create();
        $trip = Trip::factory()->create();
        $trip->destinations()->attach($destination->id);
        $response = $this->get(route('home'));

        $response->assertInertia(fn (AssertableInertia $page) => $page->component('Home')
            ->has('trips', 1)
            ->where('trips.0.id', $trip->id)
            ->where('trips.0.name', $trip->name)
        );
        $this->assertDatabaseHas('destination_trip', [
            'trip_id' => $trip->id,
            'destination_id' => $destination->id,
        ]);

        $response->assertStatus(200);
    }

    public function test_home_page_passes_the_travel_mode_and_duration_of_each_trip()
    {
        $trip = Trip::factory()->create();
        Itinerary::factory()->for($trip)->create(['type' => ItineraryType::Train, 'day_from' => 1]);
        Itinerary::factory()->for($trip)->create(['type' => ItineraryType::Stay, 'day_from' => 2, 'day_to' => 6]);

        $this->get(route('home'))
            ->assertInertia(fn (AssertableInertia $page) => $page->component('Home')
                ->has('trips', 1)
                ->where('trips.0.duration', 6)
                ->where('trips.0.travel_mode', ['value' => 'day_train', 'label' => 'Dagtrein'])
            );
    }

    public function test_a_trip_with_a_night_train_item_travels_by_night_train_and_any_other_by_day_train()
    {
        $nightTrain = Trip::factory()->create(['published_at' => today()]);
        Itinerary::factory()->for($nightTrain)->create(['type' => ItineraryType::NightTrain, 'day_from' => 1]);
        Itinerary::factory()->for($nightTrain)->create(['type' => ItineraryType::Stay, 'day_from' => 2]);

        $dayTrain = Trip::factory()->create(['published_at' => today()->subDay()]);
        Itinerary::factory()->for($dayTrain)->create(['type' => ItineraryType::Train, 'day_from' => 1]);
        Itinerary::factory()->for($dayTrain)->create(['type' => ItineraryType::Stay, 'day_from' => 2]);

        $withoutItinerary = Trip::factory()->create(['published_at' => today()->subDays(2)]);

        $this->get(route('home'))
            ->assertInertia(fn (AssertableInertia $page) => $page->component('Home')
                ->has('trips', 3)
                ->where('trips.0.id', $nightTrain->id)
                ->where('trips.0.travel_mode', ['value' => 'night_train', 'label' => 'Nachttrein'])
                ->where('trips.1.id', $dayTrain->id)
                ->where('trips.1.travel_mode', ['value' => 'day_train', 'label' => 'Dagtrein'])
                ->where('trips.2.id', $withoutItinerary->id)
                ->where('trips.2.travel_mode', ['value' => 'day_train', 'label' => 'Dagtrein'])
            );
    }

    public function test_home_page_loads_the_trips_without_a_query_per_trip()
    {
        $trips = Trip::factory()->count(3)->withHeroImage()->withImages(2)
            ->has(TripPrice::factory()->count(2), 'prices')
            ->hasAttached(Destination::factory())
            ->create();
        Itinerary::factory()->for($trips->first())->create(['type' => ItineraryType::NightTrain]);

        $this->assertNoLazyLoading(fn () => $this->get(route('home'))
            ->assertInertia(fn (AssertableInertia $page) => $page->component('Home')
                ->has('trips', 3)
                ->whereType('trips.0.price_formatted', 'string')
                ->whereType('trips.0.destinations_formatted', 'string')
                ->missing('trips.0.image_paths')
            )
        );
    }

    public function test_hero_poster_is_preloaded_only_on_home_page()
    {
        $home = $this->get(route('home'))->assertOk();
        $this->assertMatchesRegularExpression(
            '/<link rel="preload" as="image" href="[^"]*hero-poster[^"]*\.jpg"\s+fetchpriority="high">/',
            $home->getContent()
        );

        $trips = $this->get(route('trips.index'))->assertOk();
        $this->assertStringNotContainsString('hero-poster', $trips->getContent());
    }

    public function test_about_page_returns_200()
    {
        $this->get(route('about'))->assertStatus(200);
    }

    public function test_trip_show_shows_correct_trip()
    {
        $destination = Destination::factory()->create();
        $trip = Trip::factory()->create();
        $trip->destinations()->attach($destination->id);

        $response = $this->get(route('trips.show', $trip));

        $response->assertInertia(fn (AssertableInertia $page) => $page->component('Trip/Show')
            ->has('trip')
            ->where('trip.id', $trip->id)
            ->where('trip.name', $trip->name)
            ->where('trip.slug', $trip->slug)
            ->where('trip.duration', $trip->duration)
            ->missing('trip.description')
            ->where('descriptionSections.0.html', $trip->description)
            ->where('trip.featured', $trip->featured)
            ->where('trip.published_at', $trip->published_at->toISOString())
        );

        $this->assertDatabaseHas('destination_trip', [
            'trip_id' => $trip->id,
            'destination_id' => $destination->id,
        ]);
        $response->assertStatus(200);
    }

    public function test_terms_page_returns_200()
    {
        $response = $this->get(route('terms'));

        $response->assertStatus(200);
        $response->assertInertia(fn (AssertableInertia $page) => $page->component('Terms'));
    }

    public function test_terms_pdf_download_returns_pdf()
    {
        $response = $this->get(route('terms.download'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
        $response->assertDownload(TermsPdfService::FILENAME);
    }

    public function test_submit_contact_sends_contact_email()
    {
        $faker = app(Generator::class);
        Mail::fake();

        $contactData = [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'phone' => $faker->validDutchMobileNumber(),
            'text' => fake()->text(500),
        ];

        $response = $this->post(route('submit.contact', $contactData));
        $response->assertStatus(200);
        $toAddress = config('contact.mail');

        Mail::assertQueued(AdminContactFormNotificationMail::class, function ($mail) use ($toAddress, $contactData) {
            return $mail->hasTo($toAddress) &&
                   $mail->contact->name === $contactData['name'] &&
                   $mail->contact->email === $contactData['email'] &&
                   $mail->contact->text === $contactData['text'] &&
                   $mail->contact->phone === $contactData['phone'];
        });
    }
}
