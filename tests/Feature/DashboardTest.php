<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_dashboard_shows_rental_statistics(): void
    {
        $admin = User::factory()->create(['role' => 'administrator']);
        $customer1 = User::factory()->create(['role' => 'customer']);
        $customer2 = User::factory()->create(['role' => 'customer']);

        $book1 = Book::factory()->create(['title' => 'Popular Book']);
        $book2 = Book::factory()->create(['title' => 'Another Book']);

        // Create several rentals
        Rental::create([
            'user_id' => $customer1->id,
            'book_id' => $book1->id,
            'rented_at' => now()->subDays(10),
            'due_date' => now()->addDays(4),
        ]);
        Rental::create([
            'user_id' => $customer2->id,
            'book_id' => $book1->id,
            'rented_at' => now()->subDays(5),
            'due_date' => now()->addDays(9),
        ]);
        Rental::create([
            'user_id' => $customer1->id,
            'book_id' => $book2->id,
            'rented_at' => now()->subDays(3),
            'due_date' => now()->addDays(11),
        ]);

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->where('isAdministrator', true)
            ->has('statistics.total_active_rentals')
            ->has('statistics.most_rented_book')
            ->has('statistics.total_users')
            ->has('statistics.total_books')
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function customer_dashboard_shows_personal_statistics(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $book1 = Book::factory()->create(['title' => 'Book 1']);
        $book2 = Book::factory()->create(['title' => 'Book 2']);

        Rental::create([
            'user_id' => $customer->id,
            'book_id' => $book1->id,
            'rented_at' => now()->subDays(10),
            'due_date' => now()->addDays(4),
        ]);
        Rental::create([
            'user_id' => $customer->id,
            'book_id' => $book1->id,
            'rented_at' => now()->subDays(5),
            'due_date' => now()->addDays(9),
            'returned_at' => now(),
        ]);
        Rental::create([
            'user_id' => $customer->id,
            'book_id' => $book2->id,
            'rented_at' => now()->subDays(3),
            'due_date' => now()->addDays(11),
        ]);

        $response = $this->actingAs($customer)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->where('isAdministrator', false)
            ->has('statistics.active_rentals_count')
            ->has('statistics.total_rentals_count')
            ->has('statistics.most_rented_book')
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_dashboard_most_rented_book_contains_id(): void
    {
        $admin = User::factory()->create(['role' => 'administrator']);
        $book = Book::factory()->create(['title' => 'Popular Book']);
        $customer = User::factory()->create(['role' => 'customer']);

        Rental::create([
            'user_id' => $customer->id,
            'book_id' => $book->id,
            'rented_at' => now()->subDays(10),
            'due_date' => now()->addDays(4),
        ]);

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->has('statistics.most_rented_book.id')
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_dashboard_includes_users_link(): void
    {
        $admin = User::factory()->create(['role' => 'administrator']);
        User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->has('usersLink')
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_dashboard_includes_books_link(): void
    {
        $admin = User::factory()->create(['role' => 'administrator']);
        Book::factory()->create(['title' => 'Test Book']);

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->has('booksLink')
        );
    }
}


