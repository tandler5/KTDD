<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class DetailPaginationTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function book_show_page_has_paginated_history()
    {
        $user1 = User::factory()->create(['name' => 'Alice']);
        $user2 = User::factory()->create(['name' => 'Bob']);
        $book = Book::factory()->create();

        Rental::factory()->count(5)->create(['book_id' => $book->id, 'user_id' => $user1->id]);
        Rental::factory()->count(10)->create(['book_id' => $book->id, 'user_id' => $user2->id]);

        $this->actingAs($user1);

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
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_show_page_has_paginated_history()
    {
        $user = User::factory()->create();
        $book1 = Book::factory()->create(['title' => 'Book Alpha']);
        $book2 = Book::factory()->create(['title' => 'Book Beta']);

        Rental::factory()->count(5)->create(['user_id' => $user->id, 'book_id' => $book1->id]);
        Rental::factory()->count(10)->create(['user_id' => $user->id, 'book_id' => $book2->id]);

        $this->actingAs($user);

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
    }
}
