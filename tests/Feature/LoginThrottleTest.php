<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginThrottleTest extends TestCase
{
    use RefreshDatabase;

    private const ALLOWED_ATTEMPTS = 5;

    public function test_admin_login_is_throttled_after_repeated_failed_attempts(): void
    {
        $user = User::factory()->admin()->create();
        $credentials = ['email' => $user->email, 'password' => 'wrong-password'];

        for ($i = 0; $i < self::ALLOWED_ATTEMPTS; $i++) {
            $this->post(route('admin.login'), $credentials)->assertOk();
        }

        $this->post(route('admin.login'), $credentials)->assertTooManyRequests();
        $this->assertGuest();
    }

    public function test_api_login_is_throttled_after_repeated_failed_attempts(): void
    {
        $user = User::factory()->admin()->create();
        $credentials = ['email' => $user->email, 'password' => 'wrong-password'];

        for ($i = 0; $i < self::ALLOWED_ATTEMPTS; $i++) {
            $this->postJson('/api/login', $credentials)->assertUnprocessable();
        }

        $this->postJson('/api/login', $credentials)->assertTooManyRequests();
    }

    public function test_throttled_admin_login_rejects_the_correct_password(): void
    {
        $user = User::factory()->admin()->create();

        for ($i = 0; $i < self::ALLOWED_ATTEMPTS; $i++) {
            $this->post(route('admin.login'), ['email' => $user->email, 'password' => 'wrong-password']);
        }

        $this->post(route('admin.login'), ['email' => $user->email, 'password' => 'password'])
            ->assertTooManyRequests();
        $this->assertGuest();
    }
}
