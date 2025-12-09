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
    ) {
        // For test emails, create a mock subscriber to make unsubscribe links work
        if ($this->subscriber === null) {
            $this->subscriber = new NewsletterSubscriber([
                'email' => 'test@example.com',
                'name' => 'Test Ontvanger',
                'unsubscribe_token' => 'test-token',
            ]);
        }
    }

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
        return new Content(
            view: 'emails.newsletter-campaign',
            with: [
                'campaign' => $this->campaign,
                'subscriber' => $this->subscriber,
                'unsubscribeUrl' => route('newsletter.subscription.unsubscribe', $this->subscriber->unsubscribe_token ?? 'test-token'),
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
