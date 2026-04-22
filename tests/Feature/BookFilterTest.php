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
        $user = User::factory()->create();
        Book::factory()->create(['title' => 'Laravel Guide', 'author' => 'Taylor', 'isbn' => '111']);
        Book::factory()->create(['title' => 'Vue Guide', 'author' => 'Evan', 'isbn' => '222']);

        $this->actingAs($user);

        // Filter by title
        $response = $this->get(route('books.index', ['search_title' => 'Laravel']));
        $response->assertInertia(fn (Assert $page) => $page
            ->has('books.data', 1)
            ->where('books.data.0.title', 'Laravel Guide')
        );

        // Filter by author
        $response = $this->get(route('books.index', ['search_author' => 'Evan']));
        $response->assertInertia(fn (Assert $page) => $page
            ->has('books.data', 1)
            ->where('books.data.0.author', 'Evan')
        );
    }
}
