<?php

namespace Tests\Feature;

use App\Events\NewsletterSubscriptionRequested;
use App\Models\NewsletterSubscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Tests\TestCase;

class NewsletterTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_create_a_new_newsletter_subscription(): void
    {
        Event::fake([NewsletterSubscriptionRequested::class]);

        $response = $this->post(route('newsletter.subscribe'), [
            'email' => 'test@example.com',
            'name' => 'Test User',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('newsletter_subscribers', [
            'email' => 'test@example.com',
            'name' => 'Test User',
        ]);

        $subscriber = NewsletterSubscriber::where('email', 'test@example.com')->first();

        $this->assertNotNull($subscriber->subscribed_at);
        $this->assertNotNull($subscriber->confirmation_expires_at);
        $this->assertNull($subscriber->confirmed_at);
        $this->assertNull($subscriber->unsubscribed_at);

        Event::assertDispatched(NewsletterSubscriptionRequested::class);
    }

    public function test_it_can_subscribe_without_a_name(): void
    {
        Event::fake([NewsletterSubscriptionRequested::class]);

        $this->post(route('newsletter.subscribe'), [
            'email' => 'test@example.com',
        ]);

        $this->assertDatabaseHas('newsletter_subscribers', [
            'email' => 'test@example.com',
            'name' => null,
        ]);
    }

    public function test_it_can_update_an_existing_subscription(): void
    {
        Event::fake([NewsletterSubscriptionRequested::class]);

        $subscriber = NewsletterSubscriber::factory()->create([
            'email' => 'test@example.com',
            'name' => 'Oude Naam',
            'unsubscribed_at' => now(),
        ]);

        $this->post(route('newsletter.subscribe'), [
            'email' => 'test@example.com',
            'name' => 'Nieuwe Naam',
        ]);

        $subscriber->refresh();

        $this->assertEquals('Nieuwe Naam', $subscriber->name);
        $this->assertNull($subscriber->unsubscribed_at);
        $this->assertNotNull($subscriber->subscribed_at);
        $this->assertNotNull($subscriber->confirmation_expires_at);
    }

    public function test_it_dispatches_event_on_subscription(): void
    {
        Event::fake([NewsletterSubscriptionRequested::class]);

        $this->post(route('newsletter.subscribe'), [
            'email' => 'test@example.com',
            'name' => 'Test User',
        ]);

        Event::assertDispatched(NewsletterSubscriptionRequested::class, function ($event) {
            return $event->subscriber->email === 'test@example.com';
        });
    }

    public function test_it_can_confirm_a_newsletter_subscription(): void
    {
        Event::fake([NewsletterSubscriptionRequested::class]);

        $this->post(route('newsletter.subscribe'), [
            'email' => 'test@example.com',
            'name' => 'Test User',
        ]);

        $subscriber = NewsletterSubscriber::where('email', 'test@example.com')->first();

        $this->assertNotNull($subscriber);
        $this->assertNotNull($subscriber->confirmation_token);
        $this->assertNull($subscriber->confirmed_at);

        $response = $this->get(route('newsletter.confirm', $subscriber->confirmation_token));

        $response->assertStatus(200);
        $response->assertInertia(
            fn($page) => $page
                ->component('Newsletter/Confirmed')
                ->has('title')
        );

        $subscriber->refresh();
        $this->assertNotNull($subscriber->confirmed_at);
        $this->assertNull($subscriber->confirmation_expires_at);
    }

    public function test_it_cannot_confirm_with_invalid_token(): void
    {
        $response = $this->get(route('newsletter.confirm', 'ongeldige-token'));

        $response->assertStatus(404);
    }

    public function test_it_cannot_confirm_with_expired_token(): void
    {
        $subscriber = NewsletterSubscriber::factory()->create([
            'confirmation_token' => $token = Str::random(32),
            'confirmation_expires_at' => now()->subHour(),
            'confirmed_at' => null,
        ]);

        $response = $this->get(route('newsletter.confirm', $token));

        $response->assertStatus(404);
    }

    public function test_it_cannot_confirm_if_already_confirmed(): void
    {
        $subscriber = NewsletterSubscriber::factory()->create([
            'confirmation_token' => $token = Str::random(32),
            'confirmation_expires_at' => now()->addHours(24),
            'confirmed_at' => now(),
        ]);

        $response = $this->get(route('newsletter.confirm', $token));

        $response->assertStatus(404);
    }

    public function test_it_can_unsubscribe_from_newsletter(): void
    {
        Event::fake([NewsletterSubscriptionRequested::class]);

        $this->post(route('newsletter.subscribe'), [
            'email' => 'test@example.com',
            'name' => 'Test User',
        ]);

        $subscriber = NewsletterSubscriber::where('email', 'test@example.com')->first();

        $response = $this->get(route('newsletter.unsubscribe', $subscriber->unsubscribe_token));

        $response->assertStatus(200);
        $response->assertInertia(
            fn($page) => $page
                ->component('Newsletter/Unsubscribed')
                ->has('title')
        );

        $subscriber->refresh();

        $this->assertNotNull($subscriber->unsubscribed_at);
        $this->assertNull($subscriber->confirmed_at);
    }

    public function test_it_cannot_unsubscribe_with_invalid_token(): void
    {
        $response = $this->get(route('newsletter.unsubscribe', 'ongeldige-token'));

        $response->assertStatus(404);
    }

    public function test_it_cannot_unsubscribe_if_already_unsubscribed(): void
    {
        $subscriber = NewsletterSubscriber::factory()->create([
            'unsubscribe_token' => $token = Str::random(32),
            'unsubscribed_at' => now(),
        ]);

        $response = $this->get(route('newsletter.unsubscribe', $token));

        $response->assertStatus(404);
    }

    public function test_it_sets_correct_expiry_date_on_subscription(): void
    {
        config(['newsletter.confirmation_expires_after' => 48]);

        Event::fake([NewsletterSubscriptionRequested::class]);

        $this->freezeTime();

        $this->post(route('newsletter.subscribe'), [
            'email' => 'test@example.com',
        ]);

        $subscriber = NewsletterSubscriber::where('email', 'test@example.com')->first();

        $expectedExpiry = now()->addHours(48);

        $this->assertEquals(
            $expectedExpiry->timestamp,
            $subscriber->confirmation_expires_at->timestamp
        );
    }
}
