<?php

namespace Tests\Unit;

use App\Models\Book;
use App\Models\User;
use App\Models\Rental;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function book_is_not_available_if_already_rented()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        // Kniha je půjčená
        Rental::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'rented_at' => now(),
            'due_date' => now()->addDays(14),
        ]);

        $this->assertFalse($book->isAvailable());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function book_is_available_if_not_rented_or_already_returned()
    {
        $book = Book::factory()->create();
        $this->assertTrue($book->isAvailable());

        $user = User::factory()->create();
        $rental = Rental::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'rented_at' => now()->subDays(5),
            'due_date' => now()->addDays(9),
            'returned_at' => now(), // Vráceno
        ]);

        $this->assertTrue($book->isAvailable());
    }
}
