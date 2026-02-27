<?php

namespace Tests\Unit;

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_book()
    {
        $book = Book::create([
            'title' => 'Test Book',
            'author' => 'John Doe',
            'isbn' => '1234567890',
        ]);

        $this->assertDatabaseHas('books', [
            'title' => 'Test Book',
            'author' => 'John Doe',
            'isbn' => '1234567890',
        ]);
    }
}
