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

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_json_error_for_unauthenticated_rental()
    {
        $book = Book::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/rentals', [
            'book_id' => $book->id,
            'user_id' => 99999,
        ]);

        $response->assertStatus(422);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_handle_rental_validation_errors()
    {
        $response = $this->postJson('/api/rentals', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['book_id']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_uses_authenticated_user_when_user_id_not_provided()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $this->actingAs($user);

        $response = $this->postJson('/api/rentals', [
            'book_id' => $book->id,
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('rentals', [
            'book_id' => $book->id,
            'user_id' => $user->id,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_error_when_auth_user_is_null()
    {
        $book = Book::factory()->create();

        $response = $this->postJson('/api/rentals', [
            'book_id' => $book->id,
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['message' => 'User not authenticated']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_web_success_message_on_rental()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $this->actingAs($user);

        $response = $this->post('/api/rentals', [
            'book_id' => $book->id,
        ]);

        $response->assertSessionHas('success', 'Book rented successfully');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_web_error_message_on_rental_failure()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $user2 = User::factory()->create();

        $book->rentals()->create([
            'user_id' => $user2->id,
            'rented_at' => now(),
            'due_date' => now()->addDays(14),
        ]);

        $this->actingAs($user);

        $response = $this->post('/api/rentals', [
            'book_id' => $book->id,
        ]);

        $response->assertSessionHas('error', 'Book is already rented');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_web_success_message_on_return()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $book->rentals()->create([
            'user_id' => $user->id,
            'rented_at' => now(),
            'due_date' => now()->addDays(14),
        ]);

        $this->actingAs($user);

        $response = $this->post('/api/rentals/return', [
            'book_id' => $book->id,
        ]);

        $response->assertSessionHas('success', 'Book returned successfully');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_web_error_message_on_return_failure()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $this->actingAs($user);

        $response = $this->post('/api/rentals/return', [
            'book_id' => $book->id,
        ]);

        $response->assertSessionHas('error', 'Book is not currently rented');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_json_error_on_rental_failure()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $user2 = User::factory()->create();

        $book->rentals()->create([
            'user_id' => $user2->id,
            'rented_at' => now(),
            'due_date' => now()->addDays(14),
        ]);

        $this->actingAs($user);

        $response = $this->postJson('/api/rentals', [
            'book_id' => $book->id,
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['message' => 'Book is already rented']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_json_error_on_return_failure()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $this->actingAs($user);

        $response = $this->postJson('/api/rentals/return', [
            'book_id' => $book->id,
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['message' => 'Book is not currently rented']);
    }
}
