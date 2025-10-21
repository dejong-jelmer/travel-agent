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

    const EMAIL = [
        'email' => 'test@example.com',
    ];
    const NAME = [
        'name' => 'Test User',
    ];
    private array $subscriber;

    protected function setUp(): void
    {
        parent::setUp();
        Event::fake([NewsletterSubscriptionRequested::class]);
        config(['newsletter.confirmation_expires_after' => 48]);
        $this->freezeTime();
        $this->subscriber = array_merge(self::EMAIL, self::NAME);

    }

    public function test_it_can_create_a_new_newsletter_subscription(): void
    {
        $response = $this->createNewTestSubscriber();

        $response->assertStatus(200);

        $this->assertDatabaseHas('newsletter_subscribers', $this->subscriber);

        $subscriber = $this->getTestSubscriber();

        $this->assertNotNull($subscriber->subscribed_at);
        $this->assertNotNull($subscriber->confirmation_expires_at);
        $this->assertNull($subscriber->confirmed_at);
        $this->assertNull($subscriber->unsubscribed_at);

        Event::assertDispatched(NewsletterSubscriptionRequested::class);
    }

    public function test_it_can_subscribe_without_a_name(): void
    {
        $this->post(route('newsletter.subscribe'), [
            'email' => 'test@example.com',
        ]);

        $this->assertDatabaseHas('newsletter_subscribers', [
            'email' => 'test@example.com',
            'name' => null,
        ]);
    }

    public function test_it_dispatches_event_on_subscription(): void
    {
        $this->createNewTestSubscriber();

        Event::assertDispatched(NewsletterSubscriptionRequested::class, function ($event) {
            return $event->subscriber->email === self::EMAIL['email'];
        });
    }

    public function test_it_can_confirm_a_newsletter_subscription(): void
    {
        $this->createNewTestSubscriber();

        $subscriber = $this->getTestSubscriber();

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
        $this->createNewTestSubscriber();
        $subscriber = $this->getTestSubscriber();

        $subscriber->confirmation_expires_at = now()->subHour();
        $subscriber->save();

        $response = $this->get(route('newsletter.confirm', $subscriber->confirmation_token));

        $response->assertStatus(404);
    }

    public function test_it_cannot_confirm_if_already_confirmed(): void
    {
        $this->createNewTestSubscriber();
        $subscriber = $this->getTestSubscriber();

        $subscriber->confirmed_at = now();
        $subscriber->save();

        $response = $this->get(route('newsletter.confirm', $subscriber->confirmation_token));

        $response->assertStatus(404);
    }

    public function test_it_can_unsubscribe_from_newsletter(): void
    {
        $this->createNewTestSubscriber();
        $subscriber = $this->getTestSubscriber();

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
        $this->createNewTestSubscriber();
        $subscriber = $this->getTestSubscriber();

        $subscriber->unsubscribed_at = now();
        $subscriber->save();

        $subscriber = NewsletterSubscriber::where('email', self::EMAIL)->first();

        $response = $this->get(route('newsletter.unsubscribe', $subscriber->unsubscribe_token));

        $response->assertStatus(404);
    }

    public function test_it_sets_correct_expiry_date_on_subscription(): void
    {
        $this->createNewTestSubscriber();

        $subscriber = $this->getTestSubscriber();

        $expectedExpiry = now()->addHours(48);

        $this->assertEquals(
            $expectedExpiry->timestamp,
            $subscriber->confirmation_expires_at->timestamp
        );
    }

    private function createNewTestSubscriber(): TestResponse
    {
        return $this->post(route('newsletter.subscribe'), $this->subscriber);
    }

    private function getTestSubscriber(): NewsletterSubscriber
    {
        return NewsletterSubscriber::where('email', self::EMAIL)->first();
    }
}
