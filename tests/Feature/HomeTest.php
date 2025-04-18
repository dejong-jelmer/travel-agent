<?php

namespace Tests\Feature;

use App\Mail\ContactMail;
use App\Models\Country;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_displays_products()
    {
        $country = Country::factory()->create();
        $product = Product::factory()->create();
        $product->countries()->attach($country->id);

        $response = $this->get(route('home'));

        $response->assertInertia(fn (AssertableInertia $page) => $page->component('Home')
            ->has('products', 1)
            ->where('products.0.id', $product->id)
            ->where('products.0.price', $product->price)
        );
        $this->assertDatabaseHas('country_product', [
            'product_id' => $product->id,
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

    public function test_product_page_displays_correct_product()
    {
        $country = Country::factory()->create();
        $trip = Product::factory()->create();
        $trip->countries()->attach($country->id);

        $response = $this->get(route('trip.show', $trip));

        $response->assertInertia(fn (AssertableInertia $page) => $page->component('Trips/Show')
            ->has('trip')
            ->where('trip.id', $trip->id)
            ->where('trip.name', $trip->name)
            ->where('trip.slug', $trip->slug)
            ->where('trip.duration', $trip->duration)
            ->where('trip.price', $trip->price)
            ->where('trip.active', $trip->active)
            ->where('trip.featured', $trip->featured)
            ->where('trip.published_at', $trip->published_at->format('Y-m-d H:i:s'))
        );

        $this->assertDatabaseHas('country_product', [
            'product_id' => $trip->id,
            'country_id' => $country->id,
        ]);
        $response->assertStatus(200);
    }

    public function test_subtmit_contact_sends_contact_email()
    {
        Mail::fake();
        $contactData = [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'telephone' => fake()->phoneNumber(),
            'text' => fake()->text(500),
        ];

        $response = $this->post(route('contact', $contactData));
        $response->assertStatus(200);

        $toAddress = config('mail.to');
        Mail::assertQueued(ContactMail::class, function ($mail) use ($toAddress, $contactData) {
            return $mail->hasTo($toAddress) &&
                   $mail->contact->name === $contactData['name'] &&
                   $mail->contact->email === $contactData['email'] &&
                   $mail->contact->text === $contactData['text'] &&
                   $mail->contact->telephone === $contactData['telephone'];
        });
    }
}
