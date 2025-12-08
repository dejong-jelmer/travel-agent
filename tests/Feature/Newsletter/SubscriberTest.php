<?php

namespace Tests\Feature\Newsletter;

use App\Events\NewsletterSubscriptionRequested;
use App\Models\NewsletterSubscriber;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class SubscriberTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        Event::fake([NewsletterSubscriptionRequested::class]);
    }

    public function test_admin_can_view_subscriber_index(): void
    {
        NewsletterSubscriber::factory()->count(15)->create();

        $response = $this->get(route('admin.newsletter.subscribers.index'));

        $response->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Admin/Newsletter/Subscriber/Index')
                ->has('subscribers.data', 15)
                ->has('subscribers.links')
        );

        $response->assertStatus(200);
    }

    public function test_admin_can_resend_confirmation_email(): void
    {
        $hours = config('newsletter.subscription.confirmation_expires_after');

        $subscriber = NewsletterSubscriber::factory()->create();
        $response = $this->get(route('admin.newsletter.subscribers.resend', $subscriber));

        $response->assertRedirect(route('admin.newsletter.subscribers.index'));

        Event::assertDispatched(NewsletterSubscriptionRequested::class);

        $subscriber->refresh();

        $this->assertTrue(
            $subscriber->confirmation_expires_at->between(
                now(),
                now()->addHours($hours)
            )
        );
    }

    public function test_admin_can_delete_a_subscriber(): void
    {
        $subscriber = NewsletterSubscriber::factory()->create();

        $response = $this->delete(route('admin.newsletter.subscribers.destroy', $subscriber));

        $response->assertRedirect(route('admin.newsletter.subscribers.index'));

        $this->assertDatabaseMissing('newsletter_subscribers', [
            'id' => $subscriber->id,
        ]);
    }
}
