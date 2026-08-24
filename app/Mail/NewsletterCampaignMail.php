<?php

namespace App\Mail;

use App\Models\NewsletterCampaign;
use App\Models\NewsletterSubscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewsletterCampaignMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public NewsletterCampaign $campaign,
        public ?NewsletterSubscriber $subscriber = null
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->campaign->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // For test emails (no subscriber) build a throwaway subscriber so the
        // unsubscribe link renders. Created here rather than in the constructor
        // so it is never serialized by SerializesModels (an unsaved model has no
        // key to restore from and would fail the queued job).
        $subscriber = $this->subscriber ?? new NewsletterSubscriber([
            'email' => 'test@example.com',
            'name' => 'Test Ontvanger',
            'unsubscribe_token' => 'test-token',
        ]);

        return new Content(
            view: 'emails.newsletter-campaign',
            with: [
                'campaign' => $this->campaign,
                'subscriber' => $subscriber,
                'unsubscribeUrl' => route('newsletter.subscription.unsubscribe', $subscriber->unsubscribe_token ?? 'test-token'),
                'heroImage' => $this->campaign->heroImage?->public_url,
                'featuredTrips' => $this->campaign->relationLoaded('trips') ? $this->campaign->trips : [],
                // Optional variables that can be set when sending
                'ctaText' => null,
                'ctaUrl' => null,
                'socialLinks' => null,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
