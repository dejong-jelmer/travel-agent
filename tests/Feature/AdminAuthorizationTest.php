<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\NewsletterSubscriber;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdminAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public static function adminRoutes(): array
    {
        return [
            'dashboard' => ['admin.dashboard'],
            'trips' => ['admin.trips.index'],
            'bookings' => ['admin.bookings.index'],
            'trip requests' => ['admin.trip-requests.index'],
            'newsletter subscribers' => ['admin.newsletter.subscribers.index'],
            'settings' => ['admin.settings.edit'],
        ];
    }

    #[DataProvider('adminRoutes')]
    public function test_non_admin_user_cannot_view_admin_pages(string $routeName): void
    {
        $this->actingAs(User::factory()->create(['role' => UserRole::Guest]));

        $this->get(route($routeName))->assertForbidden();
    }

    public function test_non_admin_user_cannot_delete_admin_records(): void
    {
        $subscriber = NewsletterSubscriber::factory()->create();
        $this->actingAs(User::factory()->create(['role' => UserRole::Guest]));

        $this->delete(route('admin.newsletter.subscribers.destroy', $subscriber))->assertForbidden();

        $this->assertModelExists($subscriber);
    }

    public function test_non_admin_user_can_log_out(): void
    {
        $this->actingAs(User::factory()->create(['role' => UserRole::Guest]));

        $this->get(route('admin.logout'))->assertRedirect('/');

        $this->assertGuest();
    }

    public function test_admin_can_view_admin_pages(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        $this->get(route('admin.dashboard'))->assertOk();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect('/admin/login');
    }
}
