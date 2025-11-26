<?php

namespace Tests\Feature;

use Faker\Generator;
use App\Mail\AdminContactFormNotificationMail;
use App\Models\Country;
use App\Models\Trip;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_shows_trips()
    {
        $country = Country::factory()->create();
        $trip = Trip::factory()->create();
        $trip->countries()->attach($country->id);
        $response = $this->get(route('home'));

        $response->assertInertia(fn (AssertableInertia $page) => $page->component('Home')
            ->has('trips', 1)
            ->where('trips.0.id', $trip->id)
            ->where('trips.0.price', $trip->price)
        );
        $this->assertDatabaseHas('country_trip', [
            'trip_id' => $trip->id,
            'country_id' => $country->id,
        ]);

        $response->assertStatus(200);
    }

    public function test_about_page_returns_200()
    {
        $this->get(route('about'))->assertStatus(200);
    }

    public function test_contact_page_returns_200()
    {
        $this->get(route('contact'))->assertStatus(200);
    }

    public function test_trip_show_shows_correct_trip()
    {
        $country = Country::factory()->create();
        $trip = Trip::factory()->create();
        $trip->countries()->attach($country->id);

        $response = $this->get(route('trip.show', $trip));

        $response->assertInertia(fn (AssertableInertia $page) => $page->component('Trip/Show')
            ->has('trip')
            ->where('trip.id', $trip->id)
            ->where('trip.name', $trip->name)
            ->where('trip.slug', $trip->slug)
            ->where('trip.duration', $trip->duration)
            ->where('trip.price', $trip->price)
            ->where('trip.featured', $trip->featured)
            ->where('trip.published_at', $trip->published_at->toISOString())
        );

        $this->assertDatabaseHas('country_trip', [
            'trip_id' => $trip->id,
            'country_id' => $country->id,
        ]);
        $response->assertStatus(200);
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

        $response = $this->post(route('contact', $contactData));
        $response->assertStatus(200);
        $toAddress = config('contact.mail');

        Mail::assertSent(AdminContactFormNotificationMail::class, function ($mail) use ($toAddress, $contactData) {
            return $mail->hasTo($toAddress) &&
                   $mail->contact->name === $contactData['name'] &&
                   $mail->contact->email === $contactData['email'] &&
                   $mail->contact->text === $contactData['text'] &&
                   $mail->contact->phone === $contactData['phone'];
        });
    }
}
