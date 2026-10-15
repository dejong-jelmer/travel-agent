<?php

namespace Tests\Feature;

use App\DTO\ContactFormData;
use App\Events\BookingFailed;
use App\Mail\AdminBookingFailedMail;
use App\Mail\AdminBookingNotificationMail;
use App\Mail\AdminContactFormNotificationMail;
use App\Mail\AdminNewsletterSubscriptionMail;
use App\Mail\BookingConfirmationMail;
use App\Mail\NewsletterCampaignMail;
use App\Mail\NewsletterConfirmation;
use App\Mail\TripRequestConfirmationMail;
use App\Mail\TripRequestNotificationMail;
use App\Models\Booking;
use App\Models\NewsletterCampaign;
use App\Models\NewsletterSubscriber;
use App\Models\Trip;
use App\Models\TripRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MailTextVersionTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_mail_has_a_plain_text_version_without_html_entities(): void
    {
        $trip = Trip::factory()->create(['name' => "Bordeaux & de Vézère's dalen"]);
        $booking = Booking::factory()->for($trip, 'trip')->withTravelers(adults: 1)->create();
        $tripRequest = TripRequest::factory()->for($trip, 'trip')->create(['name' => "Anne D'Hondt"]);
        $subscriber = NewsletterSubscriber::factory()->create(['name' => "Jan O'Brien"]);
        $campaign = NewsletterCampaign::factory()->create(['subject' => 'Lente & treinen']);

        $mailables = [
            new BookingConfirmationMail($booking),
            new AdminBookingNotificationMail($booking),
            new AdminBookingFailedMail(new BookingFailed('Fout', 'Database error', [
                'trip_name' => $trip->name,
                'date' => '01-05-2027',
                'email' => 'klant@example.com',
            ])),
            new AdminContactFormNotificationMail(new ContactFormData("Piet O'Neil", 'piet@example.com', 'Een vraag & nog één')),
            new AdminNewsletterSubscriptionMail($subscriber),
            new NewsletterConfirmation($subscriber),
            new NewsletterCampaignMail($campaign, $subscriber),
            new TripRequestConfirmationMail($tripRequest),
            new TripRequestNotificationMail($tripRequest),
        ];

        foreach ($mailables as $mailable) {
            // The footer of the text layout
            $mailable->assertSeeInText('Meer mens. Omdat we reizen.');
            $mailable->assertDontSeeInText('&#039;');
            $mailable->assertDontSeeInText('&amp;');
            $mailable->assertDontSeeInText('trip_request.mail.');
        }
    }

    public function test_campaign_text_version_turns_the_editor_html_into_plain_text(): void
    {
        $campaign = NewsletterCampaign::factory()->create([
            'content' => '<h2>Kop &amp; meer</h2><p>Lees <a href="https://example.com/x">het verhaal</a>.</p>'
                .'<ul><li><p>Punt één</p></li><li><p>Punt twee</p></li></ul><p>Tot slot.</p>',
        ]);

        $mail = new NewsletterCampaignMail($campaign);

        $mail->assertSeeInOrderInText([
            "Kop & meer\n\n",
            "Lees het verhaal (https://example.com/x).\n\n",
            "- Punt één\n- Punt twee\n\n",
            'Tot slot.',
        ]);
        $mail->assertDontSeeInText('<');
    }
}
