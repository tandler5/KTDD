<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Rental;
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
}
