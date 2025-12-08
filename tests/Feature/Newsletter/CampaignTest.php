<?php

namespace Tests\Feature\Newsletter;

use App\Enums\Newsletter\CampaignStatus;
use App\Jobs\SendNewsletterCampaign;
use App\Mail\NewsletterCampaignMail;
use App\Models\NewsletterCampaign;
use App\Models\NewsletterSubscriber;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class CampaignTest extends TestCase
{
    use RefreshDatabase;

    private string $content;

    protected function setUp(): void
    {
        parent::setUp();

        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        Storage::fake(config('images.disk'));
        Storage::makeDirectory(config('images.directory'));

        $this->content = 'Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere. Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos himenaeos.
        Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere. Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos himenaeos.
        Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere. Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos himenaeos.
        Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere. Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos himenaeos.
        Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere. Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos himenaeos.
        Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere. Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos himenaeos.
        Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere. Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos himenaeos.';
    }

    public function test_admin_can_view_campaign_index(): void
    {
        NewsletterCampaign::factory()->count(10)->create();

        $response = $this->get(route('admin.newsletter.campaigns.index'));

        $response->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Admin/Newsletter/Campaign/Index')
                ->has('campaigns.data', 10)
                ->has('campaigns.links')
        );

        $response->assertStatus(200);
    }

    public function test_admin_can_view_campaign_create(): void
    {
        $response = $this->get(route('admin.newsletter.campaigns.create'));

        $response->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Admin/Newsletter/Campaign/Create')
        );

        $response->assertStatus(200);
    }

    public function test_admin_can_create_a_new_campaign(): void
    {
        $campaignData = [
            'hero_image' => UploadedFile::fake()->image('hero.jpg'),
            'subject' => 'This is a test subject',
            'content' => $this->content,
            'preview_text' => 'This is a test preview text',
            'status' => CampaignStatus::Draft->value,
            'scheduled_at' => null,
        ];

        $response = $this->post(route('admin.newsletter.campaigns.store'), $campaignData);

        $campaign = NewsletterCampaign::firstOrFail();

        $response->assertRedirect(route('admin.newsletter.campaigns.index'));

        $this->assertDatabaseHas('newsletter_campaigns', Arr::except($campaignData, ['hero_image']));

        // Assert hero image with hash-based storage
        $heroImage = $campaign->heroImage;
        $this->assertNotNull($heroImage);
        $this->assertEquals($campaignData['hero_image']->getClientOriginalName(), $heroImage->original_name);
        $this->assertEquals('image/jpeg', $heroImage->mime_type);
        $this->assertTrue($heroImage->is_primary);
        Storage::disk(config('images.disk'))->assertExists(config('images.directory')."/{$heroImage->path}");
    }

    public function test_admin_can_view_campaign_edit(): void
    {
        $campaign = NewsletterCampaign::factory()->create();

        $response = $this->get(route('admin.newsletter.campaigns.edit', $campaign));

        $response->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Admin/Newsletter/Campaign/Edit')
                ->has('campaign')
                ->where('campaign.id', $campaign->id)
                ->etc()
        );

        $response->assertStatus(200);
    }

    public function test_admin_can_update_an_existing_campaign(): void
    {
        $campaign = NewsletterCampaign::factory()->create();

        $updateData = [
            'id' => $campaign->id,
            'hero_image' => UploadedFile::fake()->image('hero.jpg'),
            'subject' => 'This is an updated test subject',
            'content' => $this->content,
            'preview_text' => 'This is an updated test preview text',
            'status' => CampaignStatus::Scheduled->value,
            'scheduled_at' => now()->startOfHour()->addDays(2),
        ];

        $response = $this->post(route('admin.newsletter.campaigns.update', $campaign), $updateData);

        $response->assertRedirect(route('admin.newsletter.campaigns.index'));
        $campaign->refresh();

        $this->assertEquals($updateData['subject'], $campaign->subject);
        $this->assertEquals($updateData['content'], $campaign->content);
        $this->assertEquals($updateData['preview_text'], $campaign->preview_text);
        $this->assertEquals($updateData['status'], $campaign->status->value);
        $this->assertEquals($updateData['scheduled_at'], $campaign->scheduled_at);

        // Assert featured image with hash-based storage
        $heroImage = $campaign->heroImage;
        $this->assertNotNull($heroImage);
        $this->assertEquals($updateData['hero_image']->getClientOriginalName(), $heroImage->original_name);
        $this->assertEquals('image/jpeg', $heroImage->mime_type);
        $this->assertTrue($heroImage->is_primary);
        Storage::disk(config('images.disk'))->assertExists(config('images.directory')."/{$heroImage->path}");
    }

    public function test_admin_can_delete_a_campaign(): void
    {
        $campaign = NewsletterCampaign::factory()->create();

        $response = $this->delete(route('admin.newsletter.campaigns.destroy', $campaign));

        $response->assertRedirect(route('admin.newsletter.campaigns.index'));

        $this->assertDatabaseMissing('newsletter_campaigns', [
            'id' => $campaign->id,
        ]);
    }

    public function test_admin_can_send_test_email(): void
    {
        Mail::fake();

        $campaign = NewsletterCampaign::factory()->create();
        $admin = User::first();

        $response = $this->post(route('admin.newsletter.campaigns.send-test', $campaign));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        Mail::assertSent(NewsletterCampaignMail::class, function ($mail) use ($admin, $campaign) {
            return $mail->hasTo($admin->email) &&
                   $mail->campaign->id === $campaign->id;
        });
    }

    public function test_admin_can_send_campaign_to_subscribers(): void
    {
        Queue::fake();

        $campaign = NewsletterCampaign::factory()->create([
            'status' => CampaignStatus::Draft,
        ]);

        NewsletterSubscriber::factory()->count(3)->create([
            'confirmed_at' => now(),
            'unsubscribed_at' => null,
        ]);

        $response = $this->post(route('admin.newsletter.campaigns.send', $campaign));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $campaign->refresh();

        $this->assertEquals(CampaignStatus::Queued, $campaign->status);
        $this->assertNotNull($campaign->queued_at);
        $this->assertEquals(3, $campaign->total_recipients);

        Queue::assertPushed(SendNewsletterCampaign::class);
    }

    public function test_sending_campaign_with_no_subscribers_marks_as_sent_immediately(): void
    {
        Queue::fake();

        $campaign = NewsletterCampaign::factory()->create([
            'status' => CampaignStatus::Draft,
        ]);

        $response = $this->post(route('admin.newsletter.campaigns.send', $campaign));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $campaign->refresh();

        $this->assertEquals(CampaignStatus::Sent, $campaign->status);
        $this->assertNotNull($campaign->sent_at);
        $this->assertNotNull($campaign->queued_at);
        $this->assertEquals(0, $campaign->total_recipients);
        $this->assertEquals(0, $campaign->sent_count);

        Queue::assertNothingPushed();
    }

    public function test_cannot_send_already_sent_campaign(): void
    {
        $campaign = NewsletterCampaign::factory()->create([
            'status' => CampaignStatus::Sent,
            'sent_at' => now(),
        ]);

        $response = $this->post(route('admin.newsletter.campaigns.send', $campaign));

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_cannot_send_already_queued_campaign(): void
    {
        $campaign = NewsletterCampaign::factory()->create([
            'status' => CampaignStatus::Queued,
            'queued_at' => now(),
        ]);

        $response = $this->post(route('admin.newsletter.campaigns.send', $campaign));

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_only_active_subscribers_receive_campaign(): void
    {
        Queue::fake();

        $campaign = NewsletterCampaign::factory()->create([
            'status' => CampaignStatus::Draft,
        ]);

        NewsletterSubscriber::factory()->count(2)->create([
            'confirmed_at' => now(),
            'unsubscribed_at' => null,
        ]);

        NewsletterSubscriber::factory()->create([
            'confirmed_at' => null,
            'unsubscribed_at' => null,
        ]);

        NewsletterSubscriber::factory()->create([
            'confirmed_at' => now(),
            'unsubscribed_at' => now(),
        ]);

        $response = $this->post(route('admin.newsletter.campaigns.send', $campaign));

        $response->assertRedirect();

        $campaign->refresh();

        $this->assertEquals(2, $campaign->total_recipients);
    }
}
