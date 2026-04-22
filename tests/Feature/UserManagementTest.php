<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_list_and_filter_users()
    {
        $admin = User::factory()->create(['name' => 'Admin', 'role' => 'administrator']);
        User::factory()->create(['name' => 'John Doe', 'email' => 'john@example.com']);
        User::factory()->create(['name' => 'Jane Smith', 'email' => 'jane@example.com']);

        $this->actingAs($admin);

        $response = $this->get(route('users.index', ['search_name' => 'John']));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Users/Index')
            ->has('users.data', 1)
            ->where('users.data.0.name', 'John Doe')
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_filter_users_by_email(): void
    {
        $admin = User::factory()->create(['role' => 'administrator']);
        User::factory()->create(['name' => 'John Doe', 'email' => 'john@example.com']);
        User::factory()->create(['name' => 'Jane Smith', 'email' => 'jane@example.com']);

        $this->actingAs($admin);

        $response = $this->get(route('users.index', ['search_email' => 'jane@example.com']));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Users/Index')
            ->has('users.data', 1)
            ->where('users.data.0.email', 'jane@example.com')
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_filter_users_by_role()
    {
        $admin = User::factory()->create(['role' => 'administrator']);
        User::factory()->create(['name' => 'Customer User', 'role' => 'customer']);

        $this->actingAs($admin);

        // Filter by administrator
        $response = $this->get(route('users.index', ['search_role' => 'administrator']));
        $response->assertInertia(fn (Assert $page) => $page
            ->has('users.data', 1)
            ->where('users.data.0.role', 'administrator')
        );

        // Filter by customer
        $response = $this->get(route('users.index', ['search_role' => 'customer']));
        $response->assertInertia(fn (Assert $page) => $page
            ->has('users.data', 1)
            ->where('users.data.0.role', 'customer')
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function administrator_can_update_user_role()
    {
        $admin = User::factory()->create(['role' => 'administrator']);
        $user = User::factory()->create(['role' => 'customer']);

        $this->actingAs($admin);

        $response = $this->patch(route('users.update-role', $user), [
            'role' => 'administrator',
        ]);

        $response->assertRedirect();
        $this->assertEquals('administrator', $user->fresh()->role);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function non_admin_user_triggers_abort_on_role_update()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $otherUser = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer);

        $response = $this->patch(route('users.update-role', $otherUser), [
            'role' => 'administrator',
        ]);

        $this->assertTrue($customer->isAdmin() === false);
        $response->assertStatus(403);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function customer_cannot_update_user_role()
    {
        $user1 = User::factory()->create(['role' => 'customer']);
        $user2 = User::factory()->create(['role' => 'customer']);

        $this->actingAs($user1);

        $response = $this->patch(route('users.update-role', $user2), [
            'role' => 'administrator',
        ]);

        $response->assertStatus(403);
        $this->assertEquals('customer', $user2->fresh()->role);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function non_admin_returns_403_on_role_update()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $targetUser = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer);

        $response = $this->patchJson(route('users.update-role', $targetUser), [
            'role' => 'administrator',
        ]);

        $response->assertStatus(403);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_show_user_details_with_rental_history()
    {
        $admin = User::factory()->create(['role' => 'administrator']);
        $user = User::factory()->create(['name' => 'Library Member']);
        $book = Book::factory()->create(['title' => 'Test Book']);

        Rental::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'rented_at' => now()->subDays(5),
            'due_date' => now()->addDays(9),
        ]);

        $this->actingAs($admin);

        $response = $this->get(route('users.show', $user->id));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Users/Show')
            ->where('user.name', 'Library Member')
            ->has('user.active_rentals', 1)
            ->where('user.active_rentals.0.book_title', 'Test Book')
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function administrator_cannot_change_own_role()
    {
        $admin = User::factory()->create(['role' => 'administrator']);

        $this->actingAs($admin);

        $response = $this->patch(route('users.update-role', $admin), [
            'role' => 'customer',
        ]);

        $response->assertStatus(403);
        $this->assertEquals('administrator', $admin->fresh()->role);
    }
}
