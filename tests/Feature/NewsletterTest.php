<?php

namespace Tests\Feature;

use App\Events\NewsletterSubscriptionRequested;
use App\Models\NewsletterSubscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class NewsletterTest extends TestCase
{
    use RefreshDatabase;

    private string $email;

    private string $name;

    private const CONFIRMATION_EXPIRES_AFTER = 48;

    protected function setUp(): void
    {
        parent::setUp();
        config(['newsletter.subscription.confirmation_expires_after' => self::CONFIRMATION_EXPIRES_AFTER]);
        Event::fake([NewsletterSubscriptionRequested::class]);
        $this->email = fake()->email();
        $this->name = fake()->name();
    }

    public function test_user_can_create_a_new_newsletter_subscription(): void
    {
        $response = $this->createNewTestSubscriber();

        $response->assertStatus(200);

        $this->assertDatabaseHas('newsletter_subscribers', [
            'email' => $this->email,
            'name' => $this->name,
        ]);

        $subscriber = $this->getTestSubscriber();

        $this->assertNotNull($subscriber->subscribed_at);
        $this->assertNotNull($subscriber->confirmation_expires_at);
        $this->assertNull($subscriber->confirmed_at);
        $this->assertNull($subscriber->unsubscribed_at);

        Event::assertDispatched(NewsletterSubscriptionRequested::class);
    }

    public function test_user_cannot_create_a_duplicate_newsletter_subscription(): void
    {
        $this->createNewTestSubscriber();
        $response = $this->createNewTestSubscriber();

        $response->assertStatus(302);
    }

    public function test_newsletter_subscriber_can_subscribe_without_a_name(): void
    {
        $this->post(route('newsletter.subscription.subscribe'), [
            'email' => $this->email,
        ]);

        $this->assertDatabaseHas('newsletter_subscribers', [
            'email' => $this->email,
            'name' => null,
        ]);
    }

    public function test_newsletter_subscription_dispatches_event(): void
    {
        $this->createNewTestSubscriber();

        Event::assertDispatched(NewsletterSubscriptionRequested::class, function ($event) {
            return $event->subscriber->email === $this->email;
        });
    }

    public function test_newsletter_subscription_can_be_confirmed_with_valid_token(): void
    {
        $this->createNewTestSubscriber();

        $subscriber = $this->getTestSubscriber();

        $this->assertNotNull($subscriber);
        $this->assertNotNull($subscriber->confirmation_token);
        $this->assertNull($subscriber->confirmed_at);

        $response = $this->get(route('newsletter.subscription.confirm', $subscriber->confirmation_token));

        $response->assertStatus(200);
        $response->assertInertia(
            fn ($page) => $page
                ->component('Newsletter/Confirmed')
                ->has('title')
        );

        $subscriber->refresh();
        $this->assertNotNull($subscriber->confirmed_at);
        $this->assertNull($subscriber->confirmation_expires_at);
    }

    public function test_newsletter_subscription_cannot_be_confirmed_with_invalid_token(): void
    {
        $response = $this->get(route('newsletter.subscription.confirm', 'ongeldige-token'));

        $response->assertStatus(404);
    }

    public function test_newsletter_subscription_cannot_be_confirmed_with_expired_token(): void
    {
        $this->createNewTestSubscriber();
        $subscriber = $this->getTestSubscriber();

        $subscriber->confirmation_expires_at = now()->subHour();
        $subscriber->save();

        $response = $this->get(route('newsletter.subscription.confirm', $subscriber->confirmation_token));

        $response->assertStatus(404);
    }

    public function test_newsletter_subscription_cannot_be_confirmed_if_already_confirmed(): void
    {
        $this->createNewTestSubscriber();
        $subscriber = $this->getTestSubscriber();

        $subscriber->confirmed_at = now();
        $subscriber->save();

        $response = $this->get(route('newsletter.subscription.confirm', $subscriber->confirmation_token));

        $response->assertStatus(404);
    }

    public function test_newsletter_subscriber_can_unsubscribe_from_newsletter(): void
    {
        $this->createNewTestSubscriber();
        $subscriber = $this->getTestSubscriber();

        $response = $this->get(route('newsletter.subscription.unsubscribe', $subscriber->unsubscribe_token));

        $response->assertStatus(200);
        $response->assertInertia(
            fn ($page) => $page
                ->component('Newsletter/Unsubscribed')
                ->has('title')
        );

        $subscriber->refresh();

        $this->assertNotNull($subscriber->unsubscribed_at);
        $this->assertNull($subscriber->confirmed_at);
    }

    public function test_newsletter_subscriber_cannot_unsubscribe_with_invalid_token(): void
    {
        $response = $this->get(route('newsletter.subscription.unsubscribe', 'ongeldige-token'));

        $response->assertStatus(404);
    }

    public function test_newsletter_subscriber_cannot_unsubscribe_if_already_unsubscribed(): void
    {
        $this->createNewTestSubscriber();
        $subscriber = $this->getTestSubscriber();

        $subscriber->unsubscribed_at = now();
        $subscriber->save();

        $subscriber = NewsletterSubscriber::where('email', $this->email)->first();

        $response = $this->get(route('newsletter.subscription.unsubscribe', $subscriber->unsubscribe_token));

        $response->assertStatus(404);
    }

    public function test_newsletter_subscription_sets_correct_expiry_date(): void
    {

        $this->freezeTime(function () {
            $expectedExpiry = now()->addHours(self::CONFIRMATION_EXPIRES_AFTER);
            $this->createNewTestSubscriber();
            $subscriber = $this->getTestSubscriber();

            $this->assertTrue(
                $subscriber->confirmation_expires_at->isSameSecond($expectedExpiry),
                "Expected {$expectedExpiry}, got {$subscriber->confirmation_expires_at}"
            );
        });
    }

    private function createNewTestSubscriber(): TestResponse
    {
        return $this->post(route('newsletter.subscription.subscribe'), [
            'email' => $this->email,
            'name' => $this->name,
        ]);
    }

    private function getTestSubscriber(): NewsletterSubscriber
    {
        return NewsletterSubscriber::where('email', $this->email)->first();
    }
}
