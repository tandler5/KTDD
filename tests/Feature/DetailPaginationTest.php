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
        $user = User::factory()->create();
        $book = Book::factory()->create();

        // Create 15 rentals for this book
        Rental::factory()->count(15)->create([
            'book_id' => $book->id,
            'user_id' => $user->id,
        ]);

        $this->actingAs($user);

        $response = $this->get(route('books.show', $book->id));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->has('rentals.data', 10) // Assuming 10 per page
            ->has('rentals.links')
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_show_page_has_paginated_history()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        // Create 15 rentals for this user
        Rental::factory()->count(15)->create([
            'book_id' => $book->id,
            'user_id' => $user->id,
        ]);

        $this->actingAs($user);

        $response = $this->get(route('users.show', $user->id));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->has('rentals.data', 10)
            ->has('rentals.links')
        );
    }
}
