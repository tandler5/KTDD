<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class BookFilterTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_filter_books_by_title_author_and_status()
    {
        // Use unique ISBNs to avoid conflicts with seeders
        $isbn1 = 'TEST111';
        $isbn2 = 'TEST222';
        $isbnAvail = 'TEST333';
        $isbnRented = 'TEST444';

        // Clean up any previous test data
        Book::whereIn('isbn', [$isbn1, $isbn2, $isbnAvail, $isbnRented])->delete();

        $user = User::factory()->create();
        Book::factory()->create(['title' => 'Laravel Guide', 'author' => 'Taylor', 'isbn' => $isbn1]);
        Book::factory()->create(['title' => 'Vue Guide', 'author' => 'Evan', 'isbn' => $isbn2]);

        $this->actingAs($user);

        $response = $this->get(route('books.index', ['search_title' => 'Laravel']));
        $response->assertInertia(fn (Assert $page) => $page
            ->has('books.data', 1)
            ->where('books.data.0.title', 'Laravel Guide')
        );

        $response = $this->get(route('books.index', ['search_author' => 'Evan']));
        $response->assertInertia(fn (Assert $page) => $page
            ->has('books.data', 1)
            ->where('books.data.0.author', 'Evan')
        );

        $response = $this->get(route('books.index', ['search_isbn' => $isbn1]));
        $response->assertInertia(fn (Assert $page) => $page
            ->has('books.data', 1)
            ->where('books.data.0.isbn', $isbn1)
        );

        // Test status filtering
        $availableBook = Book::factory()->create([
            'title' => 'Available Book',
            'author' => 'Author',
            'isbn' => $isbnAvail,
        ]);

        $rentedBook = Book::factory()->create([
            'title' => 'Rented Book',
            'author' => 'Author',
            'isbn' => $isbnRented,
        ]);
        $rentedBook->rentals()->create([
            'user_id' => $user->id,
            'rented_at' => now(),
            'due_date' => now()->addDays(7),
        ]);

        // Filter for available books - should contain our available book (check ISBN presence)
        $response = $this->get(route('books.index', ['search_status' => 'available']));
        $response->assertInertia(fn (Assert $page) => $page
            ->where('books.data', fn ($data) => $data
                ->collect()
                ->pluck('isbn')
                ->contains($isbnAvail)
            )
        );

        // Filter for rented books - should contain our rented book (check ISBN presence)
        $response = $this->get(route('books.index', ['search_status' => 'rented']));
        $response->assertInertia(fn (Assert $page) => $page
            ->where('books.data', fn ($data) => $data
                ->collect()
                ->pluck('isbn')
                ->contains($isbnRented)
            )
        );
    }
}
