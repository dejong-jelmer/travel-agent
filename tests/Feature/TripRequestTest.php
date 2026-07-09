<?php

namespace Tests\Feature;

use App\Enums\TripRequest\Status;
use App\Mail\TripRequestConfirmationMail;
use App\Mail\TripRequestNotificationMail;
use App\Models\Trip;
use App\Models\TripRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class TripRequestTest extends TestCase
{
    use RefreshDatabase;

    private Trip $trip;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('contact.mail', 'admin@example.com');
        Config::set('contact.full_name', 'Jelmer');

        $this->trip = Trip::factory()->create();
    }

    // Public flow - store

    public function test_visitor_can_submit_a_valid_trip_request(): void
    {
        $payload = $this->generateTripRequestPayload();

        $response = $this->post(route('trip-requests.store', $this->trip), $payload);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('trip-requests.thanks', $this->trip));

        $this->assertDatabaseHas('trip_requests', [
            'trip_id' => $this->trip->id,
            'name' => $payload['name'],
            'email' => $payload['email'],
            'phone' => $payload['phone'],
            'travelers_count' => $payload['travelers_count'],
            'departure_station' => $payload['departure_station'],
            'notes' => $payload['notes'],
            'status' => Status::New->value,
            'consent_privacy' => true,
        ]);
    }

    public function test_request_redirects_to_thanks_page_after_submit(): void
    {
        $response = $this->post(
            route('trip-requests.store', $this->trip),
            $this->generateTripRequestPayload()
        );

        $response->assertRedirect(route('trip-requests.thanks', $this->trip));
    }

    public function test_thanks_page_renders_with_trip_data(): void
    {
        $this->get(route('trip-requests.thanks', $this->trip))
            ->assertStatus(200)
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('TripRequest/Thanks')
                ->where('trip.id', $this->trip->id)
                ->where('trip.slug', $this->trip->slug)
            );
    }

    public function test_request_combines_year_and_month_into_yyyy_mm_format(): void
    {
        $year = (int) date('Y') + 1;

        $this->post(route('trip-requests.store', $this->trip), $this->generateTripRequestPayload([
            'preferred_year' => $year,
            'preferred_month' => 6,
        ]));

        $this->assertDatabaseHas('trip_requests', [
            'trip_id' => $this->trip->id,
            'preferred_month' => $year.'-06',
        ]);
    }

    public function test_consent_privacy_at_is_set_when_consent_given(): void
    {
        $this->post(
            route('trip-requests.store', $this->trip),
            $this->generateTripRequestPayload(['consent_privacy' => true])
        );

        $tripRequest = TripRequest::firstOrFail();

        $this->assertTrue($tripRequest->consent_privacy);
        $this->assertNotNull($tripRequest->consent_privacy_at);
        $this->assertEqualsWithDelta(now()->timestamp, $tripRequest->consent_privacy_at->timestamp, 5);
    }

    public function test_request_is_linked_to_correct_trip(): void
    {
        $this->post(
            route('trip-requests.store', $this->trip),
            $this->generateTripRequestPayload()
        );

        $tripRequest = TripRequest::firstOrFail();

        $this->assertEquals($this->trip->id, $tripRequest->trip_id);
        $this->assertTrue($tripRequest->trip->is($this->trip));
    }

    // Validation tests

    public function test_request_is_rejected_when_name_is_missing(): void
    {
        $response = $this->post(
            route('trip-requests.store', $this->trip),
            $this->generateTripRequestPayload(['name' => null])
        );

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseCount('trip_requests', 0);
    }

    public function test_request_is_rejected_when_email_is_missing(): void
    {
        $response = $this->post(
            route('trip-requests.store', $this->trip),
            $this->generateTripRequestPayload(['email' => null])
        );

        $response->assertSessionHasErrors('email');
        $this->assertDatabaseCount('trip_requests', 0);
    }

    public function test_request_is_rejected_when_email_is_invalid(): void
    {
        $response = $this->post(
            route('trip-requests.store', $this->trip),
            $this->generateTripRequestPayload(['email' => 'not-an-email'])
        );

        $response->assertSessionHasErrors('email');
        $this->assertDatabaseCount('trip_requests', 0);
    }

    public function test_request_is_rejected_when_consent_privacy_is_not_accepted(): void
    {
        $response = $this->post(
            route('trip-requests.store', $this->trip),
            $this->generateTripRequestPayload(['consent_privacy' => false])
        );

        $response->assertSessionHasErrors('consent_privacy');
        $this->assertDatabaseCount('trip_requests', 0);
    }

    public function test_request_is_rejected_when_preferred_month_is_out_of_range(): void
    {
        $response = $this->post(
            route('trip-requests.store', $this->trip),
            $this->generateTripRequestPayload(['preferred_month' => 13])
        );

        $response->assertSessionHasErrors('preferred_month');
        $this->assertDatabaseCount('trip_requests', 0);
    }

    public function test_request_is_rejected_when_preferred_year_is_out_of_range(): void
    {
        $response = $this->post(
            route('trip-requests.store', $this->trip),
            $this->generateTripRequestPayload(['preferred_year' => 2099])
        );

        $response->assertSessionHasErrors('preferred_year');
        $this->assertDatabaseCount('trip_requests', 0);
    }

    public function test_request_is_rejected_when_travelers_count_is_out_of_range(): void
    {
        $response = $this->post(
            route('trip-requests.store', $this->trip),
            $this->generateTripRequestPayload(['travelers_count' => 99])
        );

        $response->assertSessionHasErrors('travelers_count');
        $this->assertDatabaseCount('trip_requests', 0);
    }

    // Mail tests

    public function test_storing_request_queues_confirmation_mail_to_requester(): void
    {
        Mail::fake();

        $payload = $this->generateTripRequestPayload();

        $this->post(route('trip-requests.store', $this->trip), $payload);

        Mail::assertSent(TripRequestConfirmationMail::class, function ($mail) use ($payload) {
            return $mail->hasTo($payload['email']);
        });
    }

    public function test_storing_request_queues_admin_notification_mail(): void
    {
        Mail::fake();

        $this->post(
            route('trip-requests.store', $this->trip),
            $this->generateTripRequestPayload()
        );

        Mail::assertSent(TripRequestNotificationMail::class, function ($mail) {
            return $mail->hasTo('admin@example.com');
        });
    }

    public function test_no_mails_are_queued_when_validation_fails(): void
    {
        Mail::fake();

        $this->post(
            route('trip-requests.store', $this->trip),
            $this->generateTripRequestPayload(['email' => null])
        );

        Mail::assertNothingSent();
    }

    // Admin index tests

    public function test_admin_can_view_trip_requests_index(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        TripRequest::factory()->count(3)->for($this->trip, 'trip')->create();

        $this->get(route('admin.trip-requests.index'))
            ->assertStatus(200)
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Admin/Request/Index')
                ->has('tripRequests.data', 3)
                ->where('totalTripRequests', 3)
            );
    }

    public function test_admin_can_filter_trip_requests_by_status(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        TripRequest::factory()->count(2)->for($this->trip, 'trip')->create();
        TripRequest::factory()->contacted()->for($this->trip, 'trip')->create();

        $this->get(route('admin.trip-requests.index', ['status' => Status::New->value]))
            ->assertStatus(200)
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Admin/Request/Index')
                ->has('tripRequests.data', 2)
            );
    }

    public function test_guests_cannot_view_trip_requests_index(): void
    {
        TripRequest::factory()->for($this->trip, 'trip')->create();

        $response = $this->get(route('admin.trip-requests.index'));

        $response->assertRedirect();
        $this->assertGuest();
    }

    // Helpers

    private function generateTripRequestPayload(array $overrides = []): array
    {
        return array_replace([
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'preferred_month' => 6,
            'preferred_year' => (int) date('Y') + 1,
            'preferred_period_note' => 'Liefst rond schoolvakanties',
            'travelers_count' => 2,
            'departure_station' => 'Amsterdam Centraal',
            'notes' => 'We willen graag met de trein.',
            'consent_privacy' => true,
        ], $overrides);
    }
}
