<?php

namespace Tests\Feature;

use App\Enums\SettingKey;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\CountrySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
    }

    public function test_admin_can_save_the_default_margins(): void
    {
        $response = $this->put(route('admin.settings.update'), [
            'default_booking_margin_percentage' => '12.5',
            'default_booking_margin_amount' => '250',
            'default_booking_fee_per_person' => '75.50',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('admin.settings.edit'));

        // The percentage is stored in basis points, the amounts in euros.
        $this->assertSame('1250', (string) Setting::get(SettingKey::DefaultBookingMarginBasisPoints));
        $this->assertSame('250', (string) Setting::get(SettingKey::DefaultBookingMarginAmount));
        $this->assertSame('75.50', (string) Setting::get(SettingKey::DefaultBookingFeePerPerson));
    }

    public function test_clearing_the_default_margins_removes_them(): void
    {
        Setting::set(SettingKey::DefaultBookingMarginBasisPoints, 1250);
        Setting::set(SettingKey::DefaultBookingMarginAmount, '250');
        Setting::set(SettingKey::DefaultBookingFeePerPerson, '75');

        $this->put(route('admin.settings.update'), [
            'default_booking_margin_percentage' => null,
            'default_booking_margin_amount' => null,
            'default_booking_fee_per_person' => null,
        ])->assertSessionHasNoErrors();

        $this->assertNull(Setting::get(SettingKey::DefaultBookingMarginBasisPoints));
        $this->assertNull(Setting::get(SettingKey::DefaultBookingMarginAmount));
        $this->assertNull(Setting::get(SettingKey::DefaultBookingFeePerPerson));
    }

    public function test_the_default_margins_are_validated(): void
    {
        $response = $this->put(route('admin.settings.update'), [
            'default_booking_margin_percentage' => '96',
            'default_booking_margin_amount' => '-1',
            'default_booking_fee_per_person' => 'veel',
        ]);

        $response->assertSessionHasErrors([
            'default_booking_margin_percentage',
            'default_booking_margin_amount',
            'default_booking_fee_per_person',
        ]);
    }

    public function test_the_settings_page_shows_the_margin_as_a_percentage(): void
    {
        Setting::set(SettingKey::DefaultBookingMarginBasisPoints, 1250);
        Setting::set(SettingKey::DefaultBookingFeePerPerson, '75.50');

        $this->get(route('admin.settings.edit'))->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Admin/Settings/Edit')
                ->where('settings.default_booking_margin_percentage', 12.5)
                ->where('settings.default_booking_margin_amount', null)
                ->where('settings.default_booking_fee_per_person', '75.50')
        );
    }

    public function test_without_a_saved_percentage_the_settings_page_shows_the_fallback(): void
    {
        $this->get(route('admin.settings.edit'))->assertInertia(
            fn (AssertableInertia $page) => $page
                ->where('settings.default_booking_margin_percentage', 15)
        );
    }

    public function test_a_new_booking_starts_with_the_default_margins_in_cents(): void
    {
        Setting::set(SettingKey::DefaultBookingMarginBasisPoints, 1250);
        Setting::set(SettingKey::DefaultBookingMarginAmount, '250');
        Setting::set(SettingKey::DefaultBookingFeePerPerson, '75.50');

        $this->get(route('admin.bookings.create'))->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Admin/Booking/Create')
                ->where('default_margin_basis_points', 1250)
                ->where('default_margin_amount', 25000)
                ->where('default_fee_per_person', 7550)
        );
    }

    public function test_without_saved_amounts_a_new_booking_starts_without_them(): void
    {
        $this->get(route('admin.bookings.create'))->assertInertia(
            fn (AssertableInertia $page) => $page
                ->where('default_margin_amount', null)
                ->where('default_fee_per_person', null)
        );
    }

    public function test_the_default_margins_are_not_shared_with_every_page(): void
    {
        $this->seed(CountrySeeder::class);
        Setting::set(SettingKey::BookingFee, '25.00');
        Setting::set(SettingKey::DefaultBookingMarginBasisPoints, 1250);
        Setting::set(SettingKey::DefaultBookingMarginAmount, '250');
        Setting::set(SettingKey::DefaultBookingFeePerPerson, '75.50');

        $this->get(route('home'))->assertInertia(
            fn (AssertableInertia $page) => $page
                ->where('settings.booking_fee', '25.00')
                ->missing('settings.default_booking_margin_basis_points')
                ->missing('settings.default_booking_margin_amount')
                ->missing('settings.default_booking_fee_per_person')
        );
    }
}
