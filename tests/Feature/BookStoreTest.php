<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_cannot_create_book_with_duplicate_isbn()
    {
        $user = User::factory()->create();

        Book::factory()->create([
            'isbn' => '9780743273565'
        ]);

        $response = $this->actingAs($user)->post(route('books.store'), [
            'title' => 'New Book',
            'author' => 'Author Name',
            'isbn' => '9780743273565'
        ]);

        $response->assertSessionHasErrors('isbn');
        $this->assertEquals(1, Book::count());
    }
}
