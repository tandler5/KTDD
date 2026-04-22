<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RentalApiTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_can_create_a_rental()
    {
        $user = User::factory()->create();
        $book = Book::create([
            'title' => 'Test Book',
            'author' => 'John Doe',
            'isbn' => '1234567890',
        ]);

        $payload = [
            'book_id' => $book->id,
            'user_id' => $user->id,
            'rented_at' => now()->toDateTimeString(),
            'due_date' => now()->addDays(14)->toDateTimeString(),
        ];

        $response = $this->postJson('/api/rentals', $payload);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'book_id' => $book->id,
                'user_id' => $user->id,
            ]);

        $this->assertDatabaseHas('rentals', [
            'book_id' => $book->id,
            'user_id' => $user->id,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_error_when_book_is_already_rented()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $book = Book::factory()->create();

        // První výpůjčka
        $this->postJson('/api/rentals', [
            'book_id' => $book->id,
            'user_id' => $user1->id,
            'rented_at' => now()->toDateTimeString(),
            'due_date' => now()->addDays(14)->toDateTimeString(),
        ]);

        // Druhá výpůjčka stejné knihy jiným uživatelem
        $response = $this->postJson('/api/rentals', [
            'book_id' => $book->id,
            'user_id' => $user2->id,
            'rented_at' => now()->toDateTimeString(),
            'due_date' => now()->addDays(14)->toDateTimeString(),
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['message' => 'Book is already rented']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_can_return_a_book()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        // Půjčit
        $this->postJson('/api/rentals', [
            'book_id' => $book->id,
            'user_id' => $user->id,
        ]);

        // Vrátit
        $response = $this->postJson('/api/rentals/return', [
            'book_id' => $book->id,
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Book returned successfully']);

        $this->assertTrue($book->fresh()->isAvailable());
    }
}
