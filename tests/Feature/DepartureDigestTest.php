<?php

namespace Tests\Feature;

use App\Console\Commands\SendDepartureDigest;
use App\Enums\Booking\Status as BookingStatus;
use App\Enums\Trip\TripFormat;
use App\Mail\AdminDepartureDigestMail;
use App\Models\Booking;
use App\Models\Departure;
use App\Models\Trip;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class DepartureDigestTest extends TestCase
{
    use RefreshDatabase;

    private Trip $trip;

    protected function setUp(): void
    {
        parent::setUp();

        $this->travelTo('2027-01-10 09:00:00');
        config(['booking.mail' => 'boekingen@example.com']);
        Mail::fake();

        $this->trip = Trip::factory()->create(['format' => TripFormat::Group, 'name' => 'Vézère']);
    }

    private function departure(array $attributes = []): Departure
    {
        return Departure::factory()->for($this->trip)->create([
            'departs_on' => '2027-06-20',
            'returns_on' => '2027-06-26',
            'min_participants' => 4,
            'max_participants' => 8,
            ...$attributes,
        ]);
    }

    private function confirm(Departure $departure, int $travelers): void
    {
        Booking::factory()->onDeparture($departure, $travelers)->create(['status' => BookingStatus::Confirmed]);
    }

    public function test_no_mail_is_sent_when_no_departure_needs_attention(): void
    {
        $this->departure();
        $this->departure(['decision_date' => '2027-01-08', 'guaranteed_at' => now()]);
        $this->departure(['decision_date' => '2027-01-08', 'cancelled_at' => now(), 'cancellation_reason' => 'Strike']);

        $this->artisan('departures:send-digest')
            ->expectsOutput('No departures need attention.')
            ->assertSuccessful();

        Mail::assertNothingSent();
    }

    public function test_departure_whose_decision_is_due_within_the_warning_period_below_the_minimum_is_reported(): void
    {
        $dueSoon = $this->departure(['decision_date' => '2027-01-17']);
        $this->confirm($dueSoon, 3);
        $justOutside = $this->departure(['decision_date' => '2027-01-18']);

        $this->artisan('departures:send-digest')->assertSuccessful();

        Mail::assertSent(AdminDepartureDigestMail::class, function (AdminDepartureDigestMail $mail) use ($dueSoon, $justOutside) {
            return $mail->hasTo('boekingen@example.com')
                && $mail->departures->modelKeys() === [$dueSoon->id]
                && ! $mail->departures->contains($justOutside)
                && array_keys($mail->groups()) === ['due_soon'];
        });
    }

    public function test_departure_that_reached_its_minimum_is_not_reported_as_due_soon_but_as_waiting_for_the_guarantee(): void
    {
        $reached = $this->departure(['decision_date' => '2027-01-12']);
        $this->confirm($reached, 4);

        $this->artisan('departures:send-digest')->assertSuccessful();

        Mail::assertSent(AdminDepartureDigestMail::class, fn (AdminDepartureDigestMail $mail) => array_keys($mail->groups()) === ['awaits_guarantee']);
    }

    public function test_digest_groups_the_departures_by_what_has_to_happen(): void
    {
        $overdue = $this->departure(['decision_date' => '2027-01-08']);
        $overdueWithMinimum = $this->departure(['decision_date' => '2027-01-09']);
        $this->confirm($overdueWithMinimum, 4);
        $dueSoon = $this->departure(['decision_date' => '2027-01-15']);
        $awaitsGuarantee = $this->departure();
        $this->confirm($awaitsGuarantee, 5);
        $this->departure(['departs_on' => '2027-09-01', 'returns_on' => '2027-09-07']);

        $this->artisan('departures:send-digest')
            ->expectsOutputToContain('Reported 4 departure(s) to boekingen@example.com.')
            ->assertSuccessful();

        Mail::assertSent(AdminDepartureDigestMail::class, 1);
        Mail::assertSent(AdminDepartureDigestMail::class, function (AdminDepartureDigestMail $mail) use ($overdue, $overdueWithMinimum, $dueSoon, $awaitsGuarantee) {
            $groups = $mail->groups();

            // An overdue departure is listed once, also when its minimum is reached
            return array_keys($groups) === ['overdue', 'due_soon', 'awaits_guarantee']
                && $groups['overdue']->modelKeys() === [$overdue->id, $overdueWithMinimum->id]
                && $groups['due_soon']->modelKeys() === [$dueSoon->id]
                && $groups['awaits_guarantee']->modelKeys() === [$awaitsGuarantee->id];
        });
    }

    public function test_digest_names_every_departure_with_its_counts_and_a_link(): void
    {
        app()->setLocale('nl');
        $dueSoon = $this->departure(['decision_date' => '2027-01-15']);
        $this->confirm($dueSoon, 2);
        Booking::factory()->onDeparture($dueSoon, 1)->create(['status' => BookingStatus::Pending]);
        $overdue = $this->departure(['decision_date' => '2027-01-08', 'departs_on' => '2027-02-10', 'returns_on' => '2027-02-16']);

        $mail = new AdminDepartureDigestMail(Departure::with('trip')->orderBy('decision_date')->get());

        $mail->assertHasSubject('2 vertrekken vragen je aandacht');
        $mail->assertSeeInOrderInHtml(['Beslisdatum voorbij', '10-02-2027', 'Beslisdatum nadert, minimum niet gehaald', '20-06-2027']);
        $mail->assertSeeInHtml('Vézère');
        $mail->assertSeeInHtml('2 bevestigd, minimum 4 · 3 van 8 plekken bezet · beslisdatum 15-01-2027');
        $mail->assertSeeInHtml(route('admin.departures.show', $dueSoon));
        $mail->assertDontSeeInHtml('wacht op jouw garantie');
        $mail->assertSeeInText('2 bevestigd, minimum 4');
        $mail->assertSeeInText(route('admin.departures.show', $overdue));
        $mail->assertSeeInText(route('admin.departures.index'));
    }

    public function test_subject_is_singular_for_one_departure(): void
    {
        app()->setLocale('nl');
        $this->departure(['decision_date' => '2027-01-08']);

        (new AdminDepartureDigestMail(Departure::with('trip')->get()))->assertHasSubject('1 vertrek vraagt je aandacht');
    }

    public function test_dry_run_lists_the_departures_without_sending(): void
    {
        $this->departure(['decision_date' => '2027-01-08']);

        $this->artisan('departures:send-digest --dry-run')
            ->expectsOutputToContain('Vézère, 2027-06-20: decision overdue (0 confirmed, minimum 4, decision date 2027-01-08)')
            ->expectsOutput('[DRY RUN] Would report 1 departure(s).')
            ->assertSuccessful();

        Mail::assertNothingSent();
    }

    public function test_command_fails_without_an_address(): void
    {
        config(['booking.mail' => '']);
        $this->departure(['decision_date' => '2027-01-08']);

        $this->artisan('departures:send-digest')->assertFailed();

        Mail::assertNothingSent();
    }

    public function test_warning_period_comes_from_the_config(): void
    {
        $this->departure(['decision_date' => '2027-01-20']);

        $this->artisan('departures:send-digest')->assertSuccessful();
        Mail::assertNothingSent();

        config(['departures.decision_warning_days' => 14]);
        $this->artisan('departures:send-digest')->assertSuccessful();
        Mail::assertSent(AdminDepartureDigestMail::class, 1);
    }

    public function test_digest_is_scheduled_every_morning(): void
    {
        $event = collect(app(Schedule::class)->events())
            ->first(fn ($event) => str_contains($event->command ?? '', 'departures:send-digest'));

        $this->assertNotNull($event);
        $this->assertSame('0 7 * * *', $event->expression);
        $this->assertSame('Europe/Amsterdam', $event->timezone);
        $this->assertSame('departures:send-digest', (new SendDepartureDigest)->getName());
    }
}
