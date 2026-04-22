<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class DetailPaginationTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function book_show_page_has_paginated_history()
    {
        $admin = User::factory()->create(['role' => 'administrator']);
        $user1 = User::factory()->create(['name' => 'Alice']);
        $user2 = User::factory()->create(['name' => 'Bob']);
        $book = Book::factory()->create();

        Rental::factory()->count(5)->create(['book_id' => $book->id, 'user_id' => $user1->id]);
        Rental::factory()->count(10)->create(['book_id' => $book->id, 'user_id' => $user2->id]);

        $this->actingAs($admin);

        // Test basic pagination
        $response = $this->get(route('books.show', $book->id));
        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page->has('rentals.data', 10));

        // Test filtering by user name
        $response = $this->get(route('books.show', [$book->id, 'search_user' => 'Alice']));
        $response->assertInertia(fn (Assert $page) => $page->has('rentals.data', 5));

        // Test per_page
        $response = $this->get(route('books.show', [$book->id, 'per_page' => 50]));
        $response->assertInertia(fn (Assert $page) => $page->has('rentals.data', 15));

        // Test rented date range filtering
        $response = $this->get(route('books.show', [
            $book->id,
            'rented_from' => now()->subDay()->toDateString(),
            'rented_to' => now()->addDay()->toDateString(),
            'per_page' => 50,
        ]));
        $response->assertInertia(fn (Assert $page) => $page->has('rentals.data', 15));

        // Test returned date range filtering
        $response = $this->get(route('books.show', [
            $book->id,
            'returned_from' => now()->subDay()->toDateString(),
            'returned_to' => now()->addDay()->toDateString(),
        ]));
        $response->assertInertia(fn (Assert $page) => $page->has('rentals.data', 0));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function non_admin_cannot_see_book_history_data()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $borrower = User::factory()->create(['role' => 'customer']);
        $book = Book::factory()->create();

        Rental::factory()->count(3)->create(['book_id' => $book->id, 'user_id' => $borrower->id]);

        $response = $this->actingAs($customer)->get(route('books.show', $book->id));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->where('canViewRentalHistory', false)
            ->has('rentals.data', 0)
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_show_page_has_paginated_history()
    {
        $admin = User::factory()->create(['role' => 'administrator']);
        $user = User::factory()->create(['role' => 'customer']);
        $book1 = Book::factory()->create(['title' => 'Book Alpha']);
        $book2 = Book::factory()->create(['title' => 'Book Beta']);

        Rental::factory()->count(5)->create(['user_id' => $user->id, 'book_id' => $book1->id]);
        Rental::factory()->count(10)->create(['user_id' => $user->id, 'book_id' => $book2->id]);

        $this->actingAs($admin);

        // Test basic pagination
        $response = $this->get(route('users.show', $user->id));
        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page->has('rentals.data', 10));

        // Test filtering by book title
        $response = $this->get(route('users.show', [$user->id, 'search_book' => 'Alpha']));
        $response->assertInertia(fn (Assert $page) => $page->has('rentals.data', 5));

        // Test per_page
        $response = $this->get(route('users.show', [$user->id, 'per_page' => 50]));
        $response->assertInertia(fn (Assert $page) => $page->has('rentals.data', 15));

        // Test rented date range filtering
        $response = $this->get(route('users.show', [
            $user->id,
            'rented_from' => now()->subDay()->toDateString(),
            'rented_to' => now()->addDay()->toDateString(),
            'per_page' => 50,
        ]));
        $response->assertInertia(fn (Assert $page) => $page->has('rentals.data', 15));

        // Test returned date range filtering
        Rental::query()->update(['returned_at' => null]);
        Rental::query()->where('book_id', $book2->id)->update(['returned_at' => now()->subHours(1)]);

        $response = $this->get(route('users.show', [
            $user->id,
            'returned_from' => now()->subDay()->toDateString(),
            'returned_to' => now()->addDay()->toDateString(),
        ]));
        $response->assertInertia(fn (Assert $page) => $page->has('rentals.data', 10));
    }
}
