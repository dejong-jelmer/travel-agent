<?php

namespace App\Travel\Tests\Feature;

use App\Models\Booking;
use App\Models\User;
use App\Travel\Contracts\BookingDetails;
use App\Travel\Database\Seeders\SampleTripSeeder;
use App\Travel\Models\ShareLink;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class ShareLinkTest extends TestCase
{
    use RefreshDatabase;

    private Booking $booking;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->instance(BookingDetails::class, new class implements BookingDetails
        {
            public function title(Booking $booking): string
            {
                return 'De Vézère-vallei & Bordeaux';
            }

            public function travelerNames(Booking $booking): array
            {
                return ['Anna de Vries', 'Bram de Vries'];
            }
        });

        $this->seed(SampleTripSeeder::class);
        $this->booking = Booking::where('internal_notes', SampleTripSeeder::BOOKING_NOTE)->sole();

        // The sample trip returns on 16 August 2026, so its link works through 15 September.
        $this->travelTo('2026-08-01 12:00');
        $this->actingAs(User::factory()->admin()->create());
    }

    public function test_an_admin_makes_a_link_that_the_plan_page_shows(): void
    {
        $this->post(route('admin.bookings.share-link.store', $this->booking))->assertSessionHas('success', 'Deellink gemaakt.');

        $link = ShareLink::sole();
        $this->assertSame(40, strlen($link->token));

        $this->get(route('admin.bookings.travel-plan', $this->booking))
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('shareLink.url', route('travel.documents.shared', $link->token))
                ->where('shareLink.expires_on', '2026-09-15')
                ->where('shareLink.expired', false));
    }

    public function test_the_plan_page_has_no_link_until_one_is_made(): void
    {
        $this->get(route('admin.bookings.travel-plan', $this->booking))
            ->assertInertia(fn (AssertableInertia $page) => $page->where('shareLink', null));
    }

    public function test_the_link_opens_the_documents_without_a_session(): void
    {
        $link = ShareLink::factory()->for($this->booking)->create();
        auth()->logout();

        $response = $this->get(route('travel.documents.shared', $link))
            ->assertOk()
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow')
            ->assertSee('<meta name="robots" content="noindex, nofollow">', false)
            ->assertSee('De Vézère-vallei &amp; Bordeaux', false)
            ->assertSee('Anna de Vries, Bram de Vries')
            ->assertSee(route('travel.documents.shared.pdf', $link));

        $this->assertSame([], $response->headers->getCookies());
    }

    public function test_the_link_downloads_the_pdf(): void
    {
        $link = ShareLink::factory()->for($this->booking)->create();
        auth()->logout();

        $response = $this->get(route('travel.documents.shared.pdf', $link))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf')
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow');

        $this->assertStringContainsString("attachment; filename=reisdocumenten-{$this->booking->reference}.pdf", (string) $response->headers->get('Content-Disposition'));
    }

    public function test_a_new_link_replaces_the_old_one(): void
    {
        $old = ShareLink::factory()->for($this->booking)->create();

        $this->post(route('admin.bookings.share-link.store', $this->booking));

        $this->assertNotSame($old->token, ShareLink::sole()->token);
        $this->get(route('travel.documents.shared', $old->token))->assertNotFound();
    }

    public function test_a_revoked_link_is_not_found(): void
    {
        $link = ShareLink::factory()->for($this->booking)->create();

        $this->delete(route('admin.bookings.share-link.destroy', $this->booking))->assertSessionHas('success', 'Deellink ingetrokken.');

        $this->assertModelMissing($link);
        $this->get(route('travel.documents.shared', $link->token))->assertNotFound();
    }

    public function test_the_link_works_until_thirty_days_after_the_return(): void
    {
        $link = ShareLink::factory()->for($this->booking)->create();

        $this->travelTo('2026-09-15 23:59');
        $this->get(route('travel.documents.shared', $link))->assertOk();

        $this->travelTo('2026-09-16 00:00');
        $this->get(route('travel.documents.shared', $link))->assertNotFound();
        $this->get(route('travel.documents.shared.pdf', $link))->assertNotFound();
    }

    public function test_the_link_of_a_deleted_booking_is_not_found(): void
    {
        $link = ShareLink::factory()->for($this->booking)->create();

        $this->booking->delete();

        $this->get(route('travel.documents.shared', $link))->assertNotFound();
    }

    public function test_an_unknown_token_is_not_found(): void
    {
        $this->get(route('travel.documents.shared', 'not-a-token'))->assertNotFound();
    }

    public function test_only_an_admin_makes_or_revokes_a_link(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'guest']));

        $this->post(route('admin.bookings.share-link.store', $this->booking))->assertForbidden();
        $this->delete(route('admin.bookings.share-link.destroy', $this->booking))->assertForbidden();

        $this->assertDatabaseCount('travel_share_links', 0);
    }
}
