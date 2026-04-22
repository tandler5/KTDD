<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_default_customer_role()
    {
        $user = User::factory()->create();

        $this->assertEquals('customer', $user->refresh()->role);
    }

    public function test_user_can_be_assigned_administrator_role()
    {
        $user = User::factory()->create(['role' => 'administrator']);

        $this->assertEquals('administrator', $user->role);
        $this->assertTrue($user->isAdmin());
    }
}
