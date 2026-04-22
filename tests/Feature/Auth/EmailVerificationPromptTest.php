<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailVerificationPromptTest extends TestCase
{
    use RefreshDatabase;

    public function test_verified_user_is_redirected_to_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get('/verify-email');

        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_unverified_user_sees_verification_page(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)
            ->get('/verify-email');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Auth/VerifyEmail')
        );
    }
}
